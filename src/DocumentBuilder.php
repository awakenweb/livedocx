<?php

namespace Awakenweb\Livedocx;

/**
 * Description of DocumentBuilder
 *
 * @author Mathieu
 */
class DocumentBuilder
{

    /**
     *
     * @var Livedocx
     */
    protected $livedocx;

    /**
     *
     * @var string
     */
    protected $documentName;

    /**
     *
     * @var Document
     */
    protected $document;

    /**
     *
     * @var string
     */
    protected $fileFormat;

    /**
     *
     * @var array
     */
    protected $fields = [];

    /**
     *
     * @var array
     */
    protected $images = [];

    /**
     *
     * @var array
     */
    protected $blocks = [];

    /**
     *
     * @var string
     */
    protected $templateName;

    /**
     *
     * @var Template
     */
    protected $template;

    /**
     *
     * @var boolean
     */
    protected $cacheTemplate;

    /**
     *
     * @var string
     */
    protected $result = null;

    /**
     * @var string
     */
    protected $imagesPath;

    /**
     * @var string
     */
    protected $templatesPath;

    /**
     * @var string
     */
    protected $documentsPath;

    /**
     * Inject the Livedocx API into the document builder
     * 
     * @param \Awakenweb\Livedocx\Livedocx $livedocx
     */
    function __construct(Livedocx $livedocx, $imagesPath = __DIR__, $templatesPath = __DIR__, $documentsPath = __DIR__)
    {
        $this->livedocx = $livedocx;
        $this->imagesPäth = $imagesPäth;
        $this->templatesPath = $templatesPath;
        $this->documentsPath = $documentsPath;
    }

    /**
     * 
     * @param type $documentName
     * @return \Awakenweb\Livedocx\DocumentBuilder
     */
    public function createDocument($documentName)
    {

        $this->documentName = $documentName;

        return $this;
    }

    /**
     * 
     * @param type $fileFormat
     * @return \Awakenweb\Livedocx\DocumentBuilder
     */
    public function retrieveAs($fileFormat)
    {

        $this->fileFormat = $fileFormat;

        return $this;
    }

    /**
     * Upload the template file to livedocx.
     * It uses the remote version of the template if it exists, else it uses a local
     * file.
     * 
     * If the cacheTemplate parameter is set to false or ignored, the template is
     * not persisted on the server after upload.
     * If the cacheTemplate parameter is set to true, the local file is uploaded to
     * be used as a remote files for later usage.
     * 
     * @param string $templateName
     * @param boolean $cacheTemplate
     * @return \Awakenweb\Livedocx\DocumentBuilder
     */
    public function fromTemplate($templateName, $cacheTemplate = false)
    {

        $this->templateName = $templateName;
        $this->cacheTemplate = $cacheTemplate;

        return $this;
    }

    /**
     * You can set an array of parameters where the array index is the fieldname
     * to be merged.
     * 
     * @param array $values
     * @return \Awakenweb\Livedocx\DocumentBuilder
     */
    public function with(array $values)
    {

        foreach ($values as $fieldname => $fieldvalue) {
            $this->fields[$fieldname] = $fieldvalue;
        }

        return $this;
    }

    /**
     * You can set an array of images to use.
     * If the images are present on the server, it uses them, and uploads them else.
     * 
     * @param array $images
     * @return \Awakenweb\Livedocx\DocumentBuilder
     */
    public function withImages(array $images)
    {

        foreach ($images as $fieldname => $filename) {
            $this->images[$fieldname] = $filename;
        }

        return $this;
    }

    /**
     * Set a block of repeated values to be merged.
     * 
     * @param array $values
     * @return \Awakenweb\Livedocx\DocumentBuilder
     */
    public function withBlock($blockName, array $values)
    {
        $this->blocks[$blockName] = $values;

        return $this;
    }

    /**
     * Persist the document on your disk.
     * 
     * @param string $path
     * @return \Awakenweb\Livedocx\DocumentBuilder
     */
    public function save()
    {
        if (is_null($this->result)) {
            $this->result = $this->generateDocument();
        }
        $formatedDocumentName = $this->documentsPath . '/' . $this->documentName . '.' . $this->fileFormat;
        file_put_contents($formatedDocumentName, $this->result);
        return $this;
    }

    /**
     * Retrieve the document as a binary string.
     * i.e. to stream it for download.
     * 
     * @return string
     */
    public function get()
    {
        if (is_null($this->result)) {
            $this->result = $this->generateDocument();
        }
        return $this->result;
    }

    protected function generateDocument()
    {
        $this->prepareTemplate()
                ->prepareTemplate()
                ->prepareImages()
                ->sendValues();
        return $this->livedocx
                        ->prepare()
                        ->create()
                        ->retrieve($this->fileFormat);
    }

    /**
     * Send the fields and blocks values to the server
     * 
     * @return \Awakenweb\Livedocx\DocumentBuilder
     */
    protected function sendValues()
    {

        $this->livedocx->assign($this->fields);

        foreach ($this->blocks as $blockName => $blockValues) {
            // prepare the block
            $block = $this->livedocx->createBlock();
            $block->setName($blockName);
            $block->bind($blockValues);

            // uploads it
            $this->livedocx->assign($block);
        }
        return $this;
    }

    /**
     * check existence of the template and uploads it if it do not exist.
     * 
     * @return \Awakenweb\Livedocx\DocumentBuilder
     */
    protected function prepareTemplate()
    {


        if ($this->cacheTemplate) {
            // if the template must be cached for later re-use
            $tpl = $this->livedocx
                    ->createRemoteTemplate();
            $tpl->setName($this->templateName);

            if (!$tpl->exists()) {
                $localTpl = $this->livedocx->createLocalTemplate();
                $localTpl->setName($this->templateName, $this->templatesPath);
                $localTpl->upload();
            }
        } else {
            // if the template is only used for a one-shot generation
            $tpl = $this->livedocx->createLocalTemplate();
            $ppl->setName($this->templateName, $this->templatesPath);
        }

        $tpl->setAsActive();

        return $this;
    }

    /**
     * upload the images if they do not exist on the server
     * 
     * @return \Awakenweb\Livedocx\DocumentBuilder
     */
    protected function prepareImages()
    {
        foreach ($this->images as $imgName => $filename) {
            $img = $this->livedocx->createImage();
            $img->setFilename($filename, $this->imagesPath);
            if (!$img->exists()) {
                $img->upload();
            }
            $this->livedocx->assign($imgName, $img);
        }
        return $this;
    }

}
