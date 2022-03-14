<?php declare(strict_types=1);

namespace Sarok\Actions;

use Sarok\Logger;
use Sarok\Context;
use Sarok\Actions\Action;
use Sarok\Service\ImageService;

class SettingsImagesAction extends Action
{
    private ImageService $imageService;

    public function __construct(Logger $logger, Context $context, ImageService $imageService)
    {
        parent::__construct($logger, $context);
        $this->imageService = $imageService;
    }

    private function getUpload(string $name) : array
    {
        return $this->context->getUpload($name);
    }

    public function execute(): array
    {
        $this->log->debug('Running SettingsImagesAction');

        $user = $this->getUser();
        $userID = $user->getID();

        if ($this->isPOST()) {
            return $this->update($userID);
        }

        $path = "/userimages/$userID/";
        $fileList = $this->imageService->listDirectory($userID);
        return compact('path', 'fileList');
    }

    private function update(int $userID): array
    {
        $image = $this->getUpload('file');

        // See https://www.php.net/manual/en/features.file-upload.post-method.php for more info on array keys
        $filename = $image['name'];
        $location = $image['tmp_name'];
        $error = $image['error'];

        $this->log->debug("SettingsImagesAction: original name is '$filename', upload location is '$location', error is $error");
        
        if ($error == 0) {
            $this->imageService->uploadImage($userID, $filename, $location);
        }

        $this->setTemplateName('empty');
        $location = $this->getPOST('location', '/settings/images');
        return compact('location');
    }
}
