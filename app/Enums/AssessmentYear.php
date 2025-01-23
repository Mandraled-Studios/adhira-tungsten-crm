<?php

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;

enum AssessmentYear : string implements HasLabel {
    case Y200001 = '2000-01';
    case Y200102 = '2001-02';
    case Y200203 = '2002-03';
    case Y200304 = '2003-04';
    case Y200405 = '2004-05';
    case Y200506 = '2005-06';
    case Y200607 = '2006-07';
    case Y200708 = '2007-08';
    case Y200809 = '2008-09';
    case Y200910 = '2009-10';
    case Y201011 = '2010-11';
    case Y201112 = '2011-12';
    case Y201213 = '2012-13';
    case Y201314 = '2013-14';
    case Y201415 = '2014-15';
    case Y201516 = '2015-16';
    case Y201617 = '2016-17';
    case Y201718 = '2017-18';
    case Y201819 = '2018-19';
    case Y201920 = '2019-20';
    case Y202021 = '2020-21';
    case Y202122 = '2021-22'; 
    case Y202223 = '2022-23'; 
    case Y202324 = '2023-24'; 
    case Y202425 = '2024-25'; 
    case Y202526 = '2025-26'; 

    public function getLabel(): ?string
    {
        return $this->name;
    }
}