$(document).ready(function() {
	if (!$('#tes').length) {
		return;
	}

	var scannerStream = null;
	var scannerInterval = null;
	var barcodeDetectorInstance = null;
	var html5QrScanner = null;
	var html5QrScannerActive = false;
	var barcodeScanLocked = false;
	var scannerMode = null;
	var html5QrScriptPromise = null;

	function hasBootstrapModal() {
		return (typeof $.fn.modal === 'function');
	}

	function showScannerModal() {
		var $modal = $('#ModalScanBarcodeBarang');
		if (!$modal.length) {
			return;
		}

		if (hasBootstrapModal()) {
			$modal.modal('show');
			return;
		}

		$('body').addClass('modal-open');
		$modal
			.css('display', 'block')
			.addClass('in')
			.attr('aria-hidden', 'false');
	}

	function hideScannerModal() {
		var $modal = $('#ModalScanBarcodeBarang');
		if (!$modal.length) {
			return;
		}

		if (hasBootstrapModal()) {
			$modal.modal('hide');
			return;
		}

		$modal
			.removeClass('in')
			.css('display', 'none')
			.attr('aria-hidden', 'true');
		$('body').removeClass('modal-open');
		stopBarcodeScanner();
	}

	function setScannerStatus(message, isError) {
		var statusEl = document.getElementById('barcodeScannerStatusBarang');
		if (!statusEl) {
			return;
		}
		statusEl.innerText = message;
		statusEl.style.color = isError ? '#b90000' : '#666';
	}

	function stopBarcodeScanner() {
		barcodeScanLocked = false;

		if (scannerInterval) {
			clearInterval(scannerInterval);
			scannerInterval = null;
		}

		if (html5QrScanner && html5QrScannerActive) {
			try {
				html5QrScanner.stop().then(function() {
					html5QrScanner.clear();
				}).catch(function() {
					try {
						html5QrScanner.clear();
					} catch (e) {}
				});
			} catch (e) {}
		}
		html5QrScannerActive = false;
		html5QrScanner = null;
		scannerMode = null;

		if (scannerStream) {
			scannerStream.getTracks().forEach(function(track) {
				track.stop();
			});
			scannerStream = null;
		}

		var video = document.getElementById('barcodeScannerPreviewBarang');
		if (video) {
			video.srcObject = null;
			video.style.display = 'none';
		}

		var reader = document.getElementById('barcodeScannerReaderBarang');
		if (reader) {
			reader.style.display = 'block';
		}
	}

	function applyBarcodeSearchResult(hasilScan) {
		var cleanValue = $.trim(hasilScan || '');
		if (!cleanValue) {
			return;
		}

		var $searchInput = $('#tes_filter input[type="search"]');
		if ($searchInput.length) {
			$searchInput.val(cleanValue);
		}

		table.search(cleanValue).draw();
		setScannerStatus('Barcode terdeteksi: ' + cleanValue, false);
		hideScannerModal();
	}

	function loadHtml5QrcodeScript() {
		if (window.Html5Qrcode) {
			return Promise.resolve();
		}

		if (html5QrScriptPromise) {
			return html5QrScriptPromise;
		}

		html5QrScriptPromise = new Promise(function(resolve, reject) {
			var script = document.createElement('script');
			script.src = 'assets/js/html5-qrcode.min.js';
			script.async = true;
			script.onload = function() {
				resolve();
			};
			script.onerror = function() {
				reject(new Error('Gagal memuat html5-qrcode'));
			};
			document.head.appendChild(script);
		});

		return html5QrScriptPromise;
	}

	async function startHtml5QrcodeScanner() {
		if (!window.Html5Qrcode) {
			throw new Error('html5-qrcode belum tersedia');
		}

		var video = document.getElementById('barcodeScannerPreviewBarang');
		var reader = document.getElementById('barcodeScannerReaderBarang');
		if (video) {
			video.style.display = 'none';
		}
		if (reader) {
			reader.style.display = 'block';
		}

		html5QrScanner = new Html5Qrcode('barcodeScannerReaderBarang');
		var config = {
			fps: 10,
			qrbox: {
				width: 260,
				height: 120
			},
			aspectRatio: 1.7778,
			formatsToSupport: [
				Html5QrcodeSupportedFormats.CODE_128,
				Html5QrcodeSupportedFormats.EAN_13,
				Html5QrcodeSupportedFormats.EAN_8,
				Html5QrcodeSupportedFormats.UPC_A,
				Html5QrcodeSupportedFormats.UPC_E,
				Html5QrcodeSupportedFormats.CODABAR,
				Html5QrcodeSupportedFormats.CODE_39,
				Html5QrcodeSupportedFormats.CODE_93,
				Html5QrcodeSupportedFormats.ITF,
				Html5QrcodeSupportedFormats.QR_CODE
			],
			experimentalFeatures: {
				useBarCodeDetectorIfSupported: true
			}
		};

		await html5QrScanner.start({
			facingMode: {
				ideal: 'environment'
			}
		}, config, function(decodedText) {
			if (barcodeScanLocked) {
				return;
			}

			barcodeScanLocked = true;
			applyBarcodeSearchResult(decodedText);
		}, function() {
			// ignore per-frame decode error
		});

		html5QrScannerActive = true;
		scannerMode = 'html5-qrcode';
		setScannerStatus('Scanner aktif. Arahkan barcode ke area kamera.', false);
	}

	async function startBarcodeDetectorScanner() {
		if (!window.BarcodeDetector) {
			throw new Error('Browser tidak support BarcodeDetector.');
		}

		var video = document.getElementById('barcodeScannerPreviewBarang');
		var reader = document.getElementById('barcodeScannerReaderBarang');
		if (reader) {
			reader.style.display = 'none';
		}
		if (video) {
			video.style.display = 'block';
		}

		barcodeDetectorInstance = new BarcodeDetector({
			formats: ['code_128', 'ean_13', 'ean_8', 'qr_code']
		});

		scannerStream = await navigator.mediaDevices.getUserMedia({
			video: {
				facingMode: {
					ideal: 'environment'
				},
				width: {
					ideal: 720
				},
				height: {
					ideal: 1280
				}
			}
		});

		video.srcObject = scannerStream;
		await video.play();

		scannerInterval = setInterval(async function() {
			if (!video || video.readyState !== video.HAVE_ENOUGH_DATA) {
				return;
			}

			try {
				var detected = await barcodeDetectorInstance.detect(video);
				if (detected.length > 0 && !barcodeScanLocked) {
					barcodeScanLocked = true;
					clearInterval(scannerInterval);
					scannerInterval = null;
					applyBarcodeSearchResult(detected[0].rawValue);
				}
			} catch (err) {
				console.log('scan error', err);
			}
		}, 600);

		scannerMode = 'barcode-detector';
		setScannerStatus('Scanner aktif. Arahkan barcode ke area kamera.', false);
	}

	async function startBarcodeScanner() {
		stopBarcodeScanner();
		setScannerStatus('Menyiapkan scanner kamera...', false);

		try {
			await loadHtml5QrcodeScript();
			await startHtml5QrcodeScanner();
		} catch (err) {
			try {
				setScannerStatus('Fallback ke mode scanner bawaan browser...', false);
				await startBarcodeDetectorScanner();
			} catch (fallbackErr) {
				setScannerStatus('Scanner tidak dapat dijalankan di browser ini.', true);
			}
		}
	}

	function injectSearchBarcodeButton() {
		var filterWrapper = $('#tes_filter');
		if (!filterWrapper.length) {
			return;
		}

		if (filterWrapper.find('#btnScanBarcodeSearchBarang').length) {
			return;
		}

		var buttonHtml = '' +
			'<button type="button" id="btnScanBarcodeSearchBarang" class="btn btn-info btn-sm" style="margin-left:8px;">' +
				'<i class="fa fa-barcode"></i> Scan' +
			'</button>';

		filterWrapper.find('label').append(buttonHtml);
	}

	function getParam(name) {
		var url = new URL(window.location.href);
		return url.searchParams.get(name);
	}
	var startParam = parseInt(getParam('start') || '0', 10);
	if (isNaN(startParam) || startParam < 0) {
		startParam = 0;
	}

	var table = $('#tes').DataTable({
		processing: true,
		serverSide: true,
		autoWidth: false,
		displayStart: startParam,
		ajax: {
			"url": "modul/mod_barang/barang-serverside.php?action=table_data",
			"dataType": "JSON",
			"type": "POST"
		},
		"rowCallback": function(row, data, index) {
			let q = (data['hrgjual_barang_reguler'] - data['hrgsat_barang']) / data['hrgsat_barang'];
			
			if(q <= 0.2){
				$(row).find('td:eq(4)').css('background-color', '#ff003f');
				$(row).find('td:eq(4)').css('color', '#ffffff');
			} else if(q > 0.2 && q <= 0.25){
				$(row).find('td:eq(4)').css('background-color', '#f39c12');
				$(row).find('td:eq(4)').css('color', '#ffffff');
				
			} else if(q > 0.25 && q <= 0.3){
				$(row).find('td:eq(4)').css('background-color', '#00ff3f');
				$(row).find('td:eq(4)').css('color', '#ffffff');
				
			} else if(q > 0.3){
				$(row).find('td:eq(4)').css('background-color', '#00bfff');
				$(row).find('td:eq(4)').css('color', '#ffffff');
			}
			
		},
		columns: [{
			"data": "no",
			"className": 'text-center'
		},
		{
			"data": "nm_barang"
		},
		{
			"data": "jenisobat",
			"className": 'text-center',
			"render": function(data, type, row) {
				if (type === 'display') {
					var label = $('<div>').text(data || '-').html();
					return label + "<div style='margin-top:6px;'><button type='button' class='btn btn-xs btn-info btn-edit-jenisobat' data-id='" + (row.id_barang || '') + "'>Edit</button></div>";
				}
				return data;
			}
		},
		{
			"data": "stok_barang",
			"className": 'text-center'
		},
		{
			"data": "hrgjual_barang",
			"className": 'text-left',
		},
		{
			"data": "zataktif",
			"className": 'text-justify',
			"render": function(data, type, row) {
				if (type === 'display') {
					return (data || '') + "<div style='margin-top:6px;'><button type='button' class='btn btn-xs btn-info btn-edit-zataktif' data-id='" + (row.id_barang || '') + "'>Edit</button></div>";
				}
				return data;
			}
		},
		{
			"data": "indikasi",
			"className": 'text-justify',
			"render": function(data, type, row) {
				if (type === 'display') {
					return (data || '') + "<div style='margin-top:6px;'><button type='button' class='btn btn-xs btn-info btn-edit-indikasi' data-id='" + (row.id_barang || '') + "'>Edit</button></div>";
				}
				return data;
			}
		},
		{
			"data": "aksi",
			"className": "text-left",
			"width": "95px",
			"orderable": false,
			"searchable": false,
			"defaultContent": "",
			"visible": (typeof userLevel !== 'undefined' && userLevel == 'pemilik')
		}]
	});

		injectSearchBarcodeButton();

	table.on('draw', function() {
		$('#tes th:last-child, #tes td:last-child').css({
			'white-space': 'nowrap',
			'min-width': '95px',
			'text-align': 'left'
		});
			injectSearchBarcodeButton();
	});

	$(window).on('resize', function() {
		table.columns.adjust();
	});
	$(document).on('expanded.pushMenu collapsed.pushMenu', function() {
		table.columns.adjust();
	});

	$(document).on('click', '#btnScanBarcodeSearchBarang', function(e) {
		e.preventDefault();
		showScannerModal();

		if (!hasBootstrapModal()) {
			startBarcodeScanner();
		}
	});

	$('#ModalScanBarcodeBarang').on('shown.bs.modal', function() {
		startBarcodeScanner();
	});

	$('#ModalScanBarcodeBarang').on('hidden.bs.modal', function() {
		stopBarcodeScanner();
	});

	$(document).on('click', '#ModalScanBarcodeBarang .close, #ModalScanBarcodeBarang [data-dismiss="modal"]', function(e) {
		if (hasBootstrapModal()) {
			return;
		}
		e.preventDefault();
		hideScannerModal();
	});

	$('#tes tbody').on('click', 'a', function(e) {
		var href = $(this).attr('href') || '';
		if (href.indexOf('act=edit') === -1) {
			return;
		}
		if (href.indexOf('start=') !== -1) {
			return;
		}
		var info = table.page.info();
		var start = info ? info.start : 0;
		var separator = href.indexOf('?') !== -1 ? '&' : '?';
		$(this).attr('href', href + separator + 'start=' + start);
	});

	$('#tes tbody').on('click', '.btn-print-barcode', function(e) {
		e.preventDefault();
		var idBarang = $(this).data('id');
		if (!idBarang) {
			return;
		}

		var qtyInput = window.prompt('Jumlah barcode yang akan di-print?', '1');
		if (qtyInput === null) {
			return;
		}

		var qty = parseInt(qtyInput, 10);
		if (isNaN(qty) || qty < 1) {
			alert('Jumlah barcode harus angka minimal 1.');
			return;
		}

		if (qty > 500) {
			qty = 500;
		}

		window.open('modul/mod_barang/print_barcode.php?id=' + idBarang + '&qty=' + qty, '_blank');
	});

	var jenisobatModalRow = null;
	var jenisobatModalData = null;
	var indikasiModalRow = null;
	var indikasiModalData = null;
	var zataktifModalRow = null;
	var zataktifModalData = null;

	function showIndikasiModal() {
		if (typeof $.fn.modal === 'function') {
			$('#indikasiModal').modal('show');
		} else {
			$('body').addClass('modal-open');
			$('#indikasiModal')
				.addClass('is-open in')
				.attr('aria-hidden', 'false');
		}
	}
	function hideIndikasiModal() {
		if (typeof $.fn.modal === 'function') {
			$('#indikasiModal').modal('hide');
		} else {
			$('body').removeClass('modal-open');
			$('#indikasiModal')
				.removeClass('is-open in')
				.attr('aria-hidden', 'true');
		}
	}
	function showZataktifModal() {
		if (typeof $.fn.modal === 'function') {
			$('#zataktifModal').modal('show');
		} else {
			$('body').addClass('modal-open');
			$('#zataktifModal')
				.addClass('is-open in')
				.attr('aria-hidden', 'false');
		}
	}
	function hideZataktifModal() {
		if (typeof $.fn.modal === 'function') {
			$('#zataktifModal').modal('hide');
		} else {
			$('body').removeClass('modal-open');
			$('#zataktifModal')
				.removeClass('is-open in')
				.attr('aria-hidden', 'true');
		}
	}
	function showJenisobatModal() {
		if (typeof $.fn.modal === 'function') {
			$('#jenisobatModal').modal('show');
		} else {
			$('body').addClass('modal-open');
			$('#jenisobatModal')
				.addClass('is-open in')
				.attr('aria-hidden', 'false');
		}
	}
	function hideJenisobatModal() {
		if (typeof $.fn.modal === 'function') {
			$('#jenisobatModal').modal('hide');
		} else {
			$('body').removeClass('modal-open');
			$('#jenisobatModal')
				.removeClass('is-open in')
				.attr('aria-hidden', 'true');
		}
	}

	$(document).on('click', '.btn-edit-jenisobat', function(e) {
		e.preventDefault();
		var rowEl = $(this).closest('tr');
		jenisobatModalRow = table.row(rowEl).index();
		jenisobatModalData = table.row(rowEl).data() || {};
		var idBarang = jenisobatModalData.id_barang || $(this).data('id');
		if (!idBarang) {
			return;
		}

		jenisobatModalData.id_barang = idBarang;
		var currentValue = jenisobatModalData.jenisobat_value || '';
		var select = $('#jenisobat_modal_select');
		select.find('option[data-temp="1"]').remove();
		select.val(currentValue);
		if (currentValue && select.val() !== currentValue) {
			select.append($('<option>', {
				value: currentValue,
				text: currentValue,
				'data-temp': '1'
			}));
			select.val(currentValue);
		}
		showJenisobatModal();
	});

	$(document).on('click', '.btn-edit-indikasi', function(e) {
		e.preventDefault();
		var rowEl = $(this).closest('tr');
		indikasiModalRow = table.row(rowEl).index();
		indikasiModalData = table.row(rowEl).data() || {};
		var idBarang = indikasiModalData.id_barang || $(this).data('id');
		if (!idBarang) {
			return;
		}
		indikasiModalData.id_barang = idBarang;
		var indikasiHtml = indikasiModalData.indikasi || '';
		if (!indikasiHtml) {
			var cellHtml = table.cell(rowEl, 6).data() || '';
			var temp = $('<div>').html(cellHtml);
			temp.find('.btn-edit-indikasi').remove();
			indikasiHtml = temp.html();
		}
		showIndikasiModal();
		if (typeof CKEDITOR !== 'undefined') {
			if (CKEDITOR.instances.indikasi_modal_editor) {
				CKEDITOR.instances.indikasi_modal_editor.destroy(true);
			}
			CKEDITOR.replace('indikasi_modal_editor', {
				filebrowserBrowseUrl: '',
				filebrowserWindowWidth: 1000,
				filebrowserWindowHeight: 500
			});
			CKEDITOR.instances.indikasi_modal_editor.setData(indikasiHtml || '');
		} else {
			$('#indikasi_modal_editor').val(indikasiHtml || '');
		}
	});

	$('#jenisobatModal').on('hidden.bs.modal', function() {
		$('#jenisobat_modal_select').find('option[data-temp="1"]').remove();
		$('#jenisobat_modal_select').val('');
		jenisobatModalRow = null;
		jenisobatModalData = null;
	});
	$('#indikasiModal').on('hidden.bs.modal', function() {
		if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.indikasi_modal_editor) {
			CKEDITOR.instances.indikasi_modal_editor.destroy(true);
		}
		$('#indikasi_modal_editor').val('');
		indikasiModalRow = null;
		indikasiModalData = null;
	});
	$('#zataktifModal').on('hidden.bs.modal', function() {
		if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.zataktif_modal_editor) {
			CKEDITOR.instances.zataktif_modal_editor.destroy(true);
		}
		$('#zataktif_modal_editor').val('');
		zataktifModalRow = null;
		zataktifModalData = null;
	});
	$(document).on('click', '#jenisobatModal .close, #jenisobatModal [data-dismiss="modal"]', function(e) {
		e.preventDefault();
		hideJenisobatModal();
	});
	$(document).on('click', '#indikasiModal .close, #indikasiModal [data-dismiss="modal"]', function(e) {
		e.preventDefault();
		hideIndikasiModal();
	});
	$(document).on('click', '#zataktifModal .close, #zataktifModal [data-dismiss="modal"]', function(e) {
		e.preventDefault();
		hideZataktifModal();
	});

	$('#jenisobat_modal_save').on('click', function(e) {
		e.preventDefault();
		if (!jenisobatModalData || !jenisobatModalData.id_barang) {
			return;
		}
		var newValue = $('#jenisobat_modal_select').val() || '';
		$.ajax({
			type: 'POST',
			url: 'modul/mod_barang/aksi_barang.php?module=barang&act=update_jenisobat',
			data: {
				id_barang: jenisobatModalData.id_barang,
				jenisobat: newValue
			},
			success: function() {
				jenisobatModalData.jenisobat_value = newValue;
				jenisobatModalData.jenisobat = newValue || '-';
				table.row(jenisobatModalRow).data(jenisobatModalData).invalidate().draw(false);
				hideJenisobatModal();
			},
			error: function(xhr) {
				alert(xhr.responseText || 'Gagal menyimpan perubahan.');
			}
		});
	});

	$('#indikasi_modal_save').on('click', function(e) {
		e.preventDefault();
		if (!indikasiModalData || !indikasiModalData.id_barang) {
			return;
		}
		var newText = $('#indikasi_modal_editor').val();
		if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.indikasi_modal_editor) {
			newText = CKEDITOR.instances.indikasi_modal_editor.getData();
		}
		$.ajax({
			type: 'POST',
			url: 'modul/mod_barang/aksi_barang.php?module=barang&act=update_indikasi',
			data: {
				id_barang: indikasiModalData.id_barang,
				indikasi: newText
			},
				success: function() {
				indikasiModalData.indikasi = newText;
				table.row(indikasiModalRow).data(indikasiModalData).invalidate().draw(false);
					hideIndikasiModal();
			},
			error: function() {
				alert('Gagal menyimpan perubahan.');
			}
		});
	});

	$(document).on('click', '.btn-edit-zataktif', function(e) {
		e.preventDefault();
		var rowEl = $(this).closest('tr');
		zataktifModalRow = table.row(rowEl).index();
		zataktifModalData = table.row(rowEl).data() || {};
		var idBarang = zataktifModalData.id_barang || $(this).data('id');
		if (!idBarang) {
			return;
		}
		zataktifModalData.id_barang = idBarang;
		var zataktifHtml = zataktifModalData.zataktif || '';
		if (!zataktifHtml) {
			var cellHtml = table.cell(rowEl, 5).data() || '';
			var temp = $('<div>').html(cellHtml);
			temp.find('.btn-edit-zataktif').remove();
			zataktifHtml = temp.html();
		}
		showZataktifModal();
		if (typeof CKEDITOR !== 'undefined') {
			if (CKEDITOR.instances.zataktif_modal_editor) {
				CKEDITOR.instances.zataktif_modal_editor.destroy(true);
			}
			CKEDITOR.replace('zataktif_modal_editor', {
				filebrowserBrowseUrl: '',
				filebrowserWindowWidth: 1000,
				filebrowserWindowHeight: 500
			});
			CKEDITOR.instances.zataktif_modal_editor.setData(zataktifHtml || '');
		} else {
			$('#zataktif_modal_editor').val(zataktifHtml || '');
		}
	});

	$('#zataktif_modal_save').on('click', function(e) {
		e.preventDefault();
		if (!zataktifModalData || !zataktifModalData.id_barang) {
			return;
		}
		var newText = $('#zataktif_modal_editor').val();
		if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.zataktif_modal_editor) {
			newText = CKEDITOR.instances.zataktif_modal_editor.getData();
		}
		$.ajax({
			type: 'POST',
			url: 'modul/mod_barang/aksi_barang.php?module=barang&act=update_zataktif',
			data: {
				id_barang: zataktifModalData.id_barang,
				zataktif: newText
			},
			success: function() {
				zataktifModalData.zataktif = newText;
				table.row(zataktifModalRow).data(zataktifModalData).invalidate().draw(false);
				hideZataktifModal();
			},
			error: function() {
				alert('Gagal menyimpan perubahan.');
			}
		});
	});
});
