<?php
namespace In2code\In2studyfinder\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Sebastian Stein <sebastian.stein@in2code.de>, In2code GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Model of tt_content
 */
class TtContent extends AbstractEntity implements TtContentInterface
{
    const TABLE = 'tt_content';

    /**
     * @var string
     */
    protected $CType = null;

    /**
     * @var string
     */
    protected $header = null;

    /**
     * @var string
     */
    protected $bodytext = null;

    /**
     * @var int
     */
    protected $imageorient = null;

    /**
     * @var string
     */
    protected $imagecaption = null;

    /**
     * @var int
     */
    protected $imagecols = null;

    /**
     * @var int
     */
    protected $imageborder = null;

    /**
     * @var string
     */
    protected $media = null;

    /**
     * @var string
     */
    protected $layout = null;

    /**
     * @var int
     */
    protected $cols = null;

    /**
     * @var string
     */
    protected $subheader = null;

    /**
     * @var string
     */
    protected $headerLink = null;

    /**
     * @var string
     */
    protected $imageLink = null;

    /**
     * @var string
     */
    protected $imageZoom = null;

    /**
     * @var string
     */
    protected $altText = null;

    /**
     * @var string
     */
    protected $titleText = null;

    /**
     * @var string
     */
    protected $headerLayout = null;

    /**
     * @var string
     */
    protected $listType = null;

    /**
     * studycourse
     *
     * @var int
     */
    protected $studycourse = 0;

    /**
     * @return string
     */
    public function getCType()
    {
        return $this->CType;
    }

    /**
     * @param $ctype
     * @return void
     */
    public function setCType($ctype)
    {
        $this->CType = $ctype;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param $header
     * @return void
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }

    /**
     * @param $bodytext
     * @return void
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * @return int
     */
    public function getImageorient()
    {
        return $this->imageorient;
    }

    /**
     * @param $imageorient
     * @return void
     */
    public function setImageorient($imageorient)
    {
        $this->imageorient = $imageorient;
    }

    /**
     * @return string
     */
    public function getImagecaption()
    {
        return $this->imagecaption;
    }

    /**
     * @param $imagecaption
     * @return void
     */
    public function setImagecaption($imagecaption)
    {
        $this->imagecaption = $imagecaption;
    }

    /**
     * @return int
     */
    public function getImagecols()
    {
        return $this->imagecols;
    }

    /**
     * @param $imagecols
     * @return void
     */
    public function setImagecols($imagecols)
    {
        $this->imagecols = $imagecols;
    }

    /**
     * @return int
     */
    public function getImageborder()
    {
        return $this->imageborder;
    }

    /**
     * @param $imageborder
     * @return void
     */
    public function setImageborder($imageborder)
    {
        $this->imageborder = $imageborder;
    }

    /**
     * @return string
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param $media
     * @return void
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param $layout
     * @return void
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return int
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * @param $cols
     * @return void
     */
    public function setCols($cols)
    {
        $this->cols = $cols;
    }

    /**
     * @return string
     */
    public function getSubheader()
    {
        return $this->subheader;
    }

    /**
     * @param $subheader
     * @return void
     */
    public function setSubheader($subheader)
    {
        $this->subheader = $subheader;
    }

    /**
     * @return string
     */
    public function getHeaderLink()
    {
        return $this->headerLink;
    }

    /**
     * @param $headerLink
     * @return void
     */
    public function setHeaderLink($headerLink)
    {
        $this->headerLink = $headerLink;
    }

    /**
     * @return string
     */
    public function getImageLink()
    {
        return $this->imageLink;
    }

    /**
     * @param $imageLink
     * @return void
     */
    public function setImageLink($imageLink)
    {
        $this->imageLink = $imageLink;
    }

    /**
     * @return string
     */
    public function getImageZoom()
    {
        return $this->imageZoom;
    }

    /**
     * @param $imageZoom
     * @return void
     */
    public function setImageZoom($imageZoom)
    {
        $this->imageZoom = $imageZoom;
    }

    /**
     * @return string
     */
    public function getAltText()
    {
        return $this->altText;
    }

    /**
     * @param $altText
     * @return void
     */
    public function setAltText($altText)
    {
        $this->altText = $altText;
    }

    /**
     * @return string
     */
    public function getTitleText()
    {
        return $this->titleText;
    }

    /**
     * @param $titleText
     * @return void
     */
    public function setTitleText($titleText)
    {
        $this->titleText = $titleText;
    }

    /**
     * @return string
     */
    public function getHeaderLayout()
    {
        return $this->headerLayout;
    }

    /**
     * @param $headerLayout
     * @return void
     */
    public function setHeaderLayout($headerLayout)
    {
        $this->headerLayout = $headerLayout;
    }

    /**
     * @return string
     */
    public function getListType()
    {
        return $this->listType;
    }

    /**
     * @param $listType
     * @return void
     */
    public function setListType($listType)
    {
        $this->listType = $listType;
    }

    /**
     * Returns the studycourse
     *
     * @return int $studycourse
     */
    public function getStudycourse()
    {
        return $this->studycourse;
    }

    /**
     * Sets the studycourse
     *
     * @param int $studycourse
     * @return void
     */
    public function setStudycourse($studycourse)
    {
        $this->studycourse = $studycourse;
    }
}
