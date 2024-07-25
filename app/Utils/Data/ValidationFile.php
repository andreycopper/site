<?php
namespace Utils\Data;

use Entity\File;
use Entity\Album;
use Models\File as ModelFile;
use Exceptions\UserException;
use Models\Album as ModelAlbum;

class ValidationFile extends Validation
{
    /**
     * Check file exist in request
     * @param ?array $files - files array
     * @return bool
     */
    public static function fileCount(?array $files = []): bool
    {
        return count($files) > 1 ||
            (!empty($files[0]['name']) && !empty($files[0]['tmp_name']) && !empty($files[0]['type']) && $files[0]['error'] === 0 && $files[0]['size'] > 0);
    }

    /**
     * Check incoming file array
     * @param array $files - file array
     * @return bool
     * @throws UserException
     */
    public static function files(array $files): bool
    {
        foreach ($files as $file) {
            self::isValidFileName($file['name']);
            self::isNoFileError($file['error']);
            self::isValidFileSize($file['size']);
            self::isFileExist($file['tmp_name']);
            self::isValidFileType($file['type']);
        }

        return true;
    }

    /**
     * Check file name
     * @param ?string $name - file name
     * @return bool
     * @throws UserException
     */
    private static function isValidFileName(?string $name = null): bool
    {
        if (empty($name)) throw new UserException(ModelFile::FILE_EMPTY);
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($extension, ModelFile::ALLOWED_EXTENSIONS)) throw new UserException(ModelFile::FILE_TYPE_IS_NOT_ALLOWED);
        return true;
    }

    /**
     * Check file upload error
     * @param int $error - error code
     * @return bool
     * @throws UserException
     */
    private static function isNoFileError(int $error = 0): bool
    {
        if ($error !== 0) throw new UserException(ModelFile::ERRORS[$error]);
        return true;
    }

    /**
     * Check file size
     * @param int $size - file size
     * @return bool
     * @throws UserException
     */
    private static function isValidFileSize(int $size = 0): bool
    {
        if ($size === 0) throw new UserException(ModelFile::FILE_EMPTY);
        if ($size > 50000000) throw new UserException(ModelFile::FILE_SIZE_TOO_BIG);
        return true;
    }

    /**
     * Check exist uploaded file in temp directory
     * @param ?string $path - file path
     * @return bool
     * @throws UserException
     */
    public static function isFileExist(?string $path = null): bool
    {
        if (empty($path)) throw new UserException(ModelFile::FILE_EMPTY);
        if (!is_file($path)) throw new UserException(ModelFile::FILE_NOT_UPLOADED);
        return true;
    }

    /**
     * Check file type
     * @param ?string $type - file type
     * @return bool
     * @throws UserException
     */
    private static function isValidFileType(?string $type = null): bool
    {
        if (empty($type)) throw new UserException(ModelFile::FILE_EMPTY);
        if (!self::isAudioFile($type) && !self::isImageFile($type) && !self::isUserFile($type))
            throw new UserException(ModelFile::FILE_TYPE_IS_NOT_ALLOWED);

        return true;
    }

    /**
     * Check is audio file
     * @param string $type - file type
     * @return bool
     */
    public static function isAudioFile(string $type): bool
    {
        return in_array($type, ModelFile::ALLOWED_TYPES['audio']);
    }

    /**
     * Check is image file
     * @param string $type - file type
     * @return bool
     */
    public static function isImageFile(string $type): bool
    {
        return in_array($type, ModelFile::ALLOWED_TYPES['image']);
    }

    /**
     * Check is user file
     * @param string $type - file type
     * @return bool
     */
    public static function isUserFile(string $type): bool
    {
        return in_array($type, ModelFile::ALLOWED_TYPES['file']);
    }

    /**
     * Check file
     * @param ?File $file - user file
     * @return bool
     * @throws UserException
     */
    public static function isValidFile(?File $file): bool
    {
        if (empty($file) || empty($file->getId())) throw new UserException(ModelFile::FILE_NOT_FOUND);
        if (empty($file->getAlbumId())) throw new UserException(ModelAlbum::ALBUM_NOT_FOUND);
        return true;
    }

    /**
     * Check album
     * @param ?Album $album - user album
     * @return bool
     * @throws UserException
     */
    public static function isValidAlbum(?Album $album): bool
    {
        if (empty($album) || empty($album->getId())) throw new UserException(ModelAlbum::ALBUM_NOT_FOUND);
        return true;
    }
}
