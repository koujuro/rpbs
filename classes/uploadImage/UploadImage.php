<?php

class UploadImage {
    private $targetFile;
    private $fileSize;
    private $fileName;
    private $tmpName;
    private $fileExtension;
    private $date;
    private $allowedFiles;
    private $errorMessage;

    public function __construct() {
        $this->targetFile = "";
        $this->fileSize = "";
        $this->fileName = "";
        $this->tmpName = "";
        $this->fileExtension = "";
        $this->date = "";
        $this->allowedFiles = array("jpg", "jpeg", "png", "bmp");
        $this->errorMessage = "";
    }

    public function setParameters($fileName, $tmpName, $fileSize) {
        $this->fileName = $fileName;
        $this->fileSize = $fileSize;
        $this->tmpName = $tmpName;
        $this->fileExtension = $this->getFileExtension();
        $this->date = strtotime(date("d.m.Y H:i:s"));
        $this->fileName = $this->changeFileName();
        $this->targetFile = $this->createTargetFile();
    }

    private function getFileExtension() {
        return pathinfo($this->fileName,PATHINFO_EXTENSION);
    }

    private function changeFileName() {
        return date("dmY_His", $this->date) . "_" . rand(123456789, 987654321);
    }

    private function createTargetFile() {
        return __DIR__ . "/../../assets/img/uploads/" . $this->fileName . "." . $this->fileExtension;
    }

    public function checkParameters() {
        try {
            $this->checkFunctions();
            return "";
        } catch (RFLFileUploadException $exception) {
            //echo $exception->getErrorMessage();
            return $exception->getErrorMessage();
        }
    }

    private function checkFunctions() {
        $this->checkIfFileIsSetted();
        $this->checkFileExtension();
        $this->checkFileSize();
    }

    private function checkIfFileIsSetted() {
        if($this->tmpName === "")
            throw new RFLFileUploadException("File is not setted.");
    }

    private function checkFileExtension() {
        if(!in_array($this->fileExtension, $this->allowedFiles)) {
            $this->createErrorMessage($this->errorMessage, $this->allowedFiles);
            throw new RFLFileUploadException($this->errorMessage);
        }
    }

    private function checkFileSize() {
        if($this->fileSize > 41943040)
            throw new RFLFileUploadException("File is too large.");
    }

    protected function createErrorMessage(&$errorMessage, $allowedFiles) {
        $errorMessage = "File is not allowed. Only ";
        foreach ($allowedFiles as $allowedFile)
            $errorMessage .= $allowedFile . ", ";
        $errorMessage = rtrim($errorMessage, ", ");
        $errorMessage .= " are allowed.";
    }

    public function uploadFile() {
        try{
            $this->tryToUpload();
            return true;
        } catch (RFLFileUploadException $exception) {
            echo $exception->getErrorMessage();
            return false;
        }
    }

    private function tryToUpload() {
        if(!move_uploaded_file($this->tmpName, $this->targetFile))
            throw new RFLFileUploadException("Sorry, there was an error uploading your file.");
    }

    public function getFileName() {
        return $this->fileName;
    }
}