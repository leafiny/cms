<?php

declare(strict_types=1);

/**
 * Class Trumbowyg_Page_Backend_File_Upload
 */
class Trumbowyg_Page_Backend_File_Upload extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     * @throws Exception
     */
    public function action(): void
    {
        parent::action();

        if (!isset($_FILES['file'])) {
            print json_encode(['success' => false]);
            return;
        }

        /** @var array $image */
        $image = $_FILES['file'];

        /** @var Backend_Helper_Data $helper */
        $helper = App::getSingleton('helper', 'admin_data');
        /** @var Core_Helper_File $fileHelper */
        $fileHelper = App::getObject('helper_file');

        try {
            $fileHelper->uploadFile($image, $helper->getMediaDir() . $image['name']);
        } catch (Exception $exception) {
            print json_encode(
                [
                    'success' => false,
                    'error'   => $exception->getMessage()
                ]
            );
            return;
        }

        print json_encode(
            [
                'success' => true,
                'url'     => US . Core_Helper::MEDIA_DIRECTORY . US . $image['name'],
            ]
        );
    }
}
