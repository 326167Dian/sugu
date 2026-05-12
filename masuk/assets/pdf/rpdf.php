<?php
require('../../assets/pdf/fpdf.php');

class RPDF extends FPDF {
    var $angle = 0;


    function TextWithDirection($x, $y, $txt, $direction='R')
    {
        if ($direction=='R')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        elseif ($direction=='L')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        elseif ($direction=='U')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        elseif ($direction=='D')
            $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        else
            $s=sprintf('BT %.2F %.2F Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        if ($this->ColorFlag)
            $s='q '.$this->TextColor.' '.$s.' Q';
        $this->_out($s);
    }

    function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle=0)
    {
        $font_angle+=90+$txt_angle;
        $txt_angle*=M_PI/180;
        $font_angle*=M_PI/180;

        $txt_dx=cos($txt_angle);
        $txt_dy=sin($txt_angle);
        $font_dx=cos($font_angle);
        $font_dy=sin($font_angle);

        $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',$txt_dx,$txt_dy,$font_dx,$font_dy,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        if ($this->ColorFlag)
            $s='q '.$this->TextColor.' '.$s.' Q';
        $this->_out($s);
    }
    
    function Rotate($angle, $x = -1, $y = -1)
{
    if ($x == -1)
        $x = $this->x;
    if ($y == -1)
        $y = $this->y;
    if ($this->angle != 0)
        $this->_out('Q');
    $this->angle = $angle;
    if ($angle != 0)
    {
        $angle *= M_PI / 180;
        $c = cos($angle);
        $s = sin($angle);
        $cx = $x * $this->k;
        $cy = ($this->h - $y) * $this->k;
        $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.5F %.5F cm 1 0 0 1 %.5F %.5F cm',
            $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
    }
}

function _endpage()
{
    if ($this->angle != 0)
    {
        $this->angle = 0;
        $this->_out('Q');
    }
    parent::_endpage();
}

function RotatedImage($file, $x, $y, $w, $h, $angle)
{
    // put rotation before image
    $this->Rotate($angle, $x, $y);
    $this->Image($file, $x, $y, $w, $h);
    $this->Rotate(0); // reset rotation
}


}
?>