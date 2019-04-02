<?php

namespace In2code\In2studyfinder\Domain\Model;

/**
 * Model of tt_content
 */
interface TtContentInterface
{
    /**
     * @return string
     */
    public function getCType();

    /**
     * @param $ctype
     * @return void
     */
    public function setCType($ctype);

    /**
     * @return string
     */
    public function getHeader();

    /**
     * @param $header
     * @return void
     */
    public function setHeader($header);

    /**
     * @return string
     */
    public function getBodytext();

    /**
     * @param $bodytext
     * @return void
     */
    public function setBodytext($bodytext);

    /**
     * @return int
     */
    public function getImageorient();

    /**
     * @param $imageorient
     * @return void
     */
    public function setImageorient($imageorient);

    /**
     * @return string
     */
    public function getImagecaption();

    /**
     * @param $imagecaption
     * @return void
     */
    public function setImagecaption($imagecaption);

    /**
     * @return int
     */
    public function getImagecols();

    /**
     * @param $imagecols
     * @return void
     */
    public function setImagecols($imagecols);

    /**
     * @return int
     */
    public function getImageborder();

    /**
     * @param $imageborder
     * @return void
     */
    public function setImageborder($imageborder);

    /**
     * @return string
     */
    public function getMedia();

    /**
     * @param $media
     * @return void
     */
    public function setMedia($media);

    /**
     * @return string
     */
    public function getLayout();

    /**
     * @param $layout
     * @return void
     */
    public function setLayout($layout);

    /**
     * @return int
     */
    public function getCols();

    /**
     * @param $cols
     * @return void
     */
    public function setCols($cols);

    /**
     * @return string
     */
    public function getSubheader();

    /**
     * @param $subheader
     * @return void
     */
    public function setSubheader($subheader);

    /**
     * @return string
     */
    public function getHeaderLink();

    /**
     * @param $headerLink
     * @return void
     */
    public function setHeaderLink($headerLink);

    /**
     * @return string
     */
    public function getImageLink();

    /**
     * @param $imageLink
     * @return void
     */
    public function setImageLink($imageLink);

    /**
     * @return string
     */
    public function getImageZoom();

    /**
     * @param $imageZoom
     * @return void
     */
    public function setImageZoom($imageZoom);

    /**
     * @return string
     */
    public function getAltText();

    /**
     * @param $altText
     * @return void
     */
    public function setAltText($altText);

    /**
     * @return string
     */
    public function getTitleText();

    /**
     * @param $titleText
     * @return void
     */
    public function setTitleText($titleText);

    /**
     * @return string
     */
    public function getHeaderLayout();

    /**
     * @param $headerLayout
     * @return void
     */
    public function setHeaderLayout($headerLayout);

    /**
     * @return string
     */
    public function getListType();

    /**
     * @param $listType
     * @return void
     */
    public function setListType($listType);

    /**
     * Returns the studycourse
     *
     * @return int $studycourse
     */
    public function getStudycourse();

    /**
     * Sets the studycourse
     *
     * @param int $studycourse
     * @return void
     */
    public function setStudycourse($studycourse);
}
