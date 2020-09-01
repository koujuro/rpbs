<?php

/**
 * Simple custom exception for errors in file_upload package.
 */
class RFLFileUploadException extends Exception {
    /**
     * Message that will be returned in case of exception.
     */
    private $errorMessage;

    /**
     * RFLFileUploadException constructor.
     * @param $errorMessage
     */
    public function __construct($errorMessage) {
        $this->errorMessage = $errorMessage;
    }

    /**
     * The function will return message.
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }
}