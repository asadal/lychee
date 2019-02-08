<?php

namespace App\Image;

use App\Configs;
use App\Logs;

class ImagickHandler implements ImageHandlerInterface
{
	/**
	 * @var int
	 */
	private $compressionQuality = null;



	/**
	 * @{inheritdoc}
	 */
	public function __construct()
	{
	}



	private function get_quality()
	{
		if ($this->compressionQuality == null) {
			$this->compressionQuality = Configs::get_value('compression_quality');
		}
		return $this->compressionQuality;
	}



	/**
	 * @{inheritdoc}
	 */
	public function scale(
		string $source,
		string $destination,
		int $newWidth,
		int $newHeight
	): bool
	{
		try {
			// Read image
			$image = new \Imagick();
			$image->readImage($source);
			$image->setImageCompressionQuality($this->get_quality());
			$image->setImageFormat('jpeg');

			// Remove metadata to save some bytes
			$image->stripImage();

			$image->scaleImage($newWidth, $newHeight, ($newWidth != 0));
			$image->writeImage($destination);
			Logs::notice(__METHOD__, __LINE__, 'Saving thumb to '.$destination);
			$image->clear();
			$image->destroy();
		}
		catch (ImagickException $exception) {
			Logs::error(__METHOD__, __LINE__, $exception->getMessage());
			return false;
		}

		return true;
	}



	/**
	 * @{inheritdoc}
	 */
	public function crop(
		string $source,
		string $destination,
		int $newWidth,
		int $newHeight
	): bool
	{
		try {
			$image = new \Imagick();
			$image->readImage($source);
			$image->setImageCompressionQuality($this->get_quality());
			$image->setImageFormat('jpeg');

			// Remove metadata to save some bytes
			$image->stripImage();

			$image->cropThumbnailImage($newWidth, $newHeight);
			$image->writeImage($destination);
			Logs::notice(__METHOD__, __LINE__, 'Saving thumb to '.$destination);
			$image->clear();
			$image->destroy();
		}
		catch (ImagickException $exception) {
			Logs::error(__METHOD__, __LINE__, $exception->getMessage());
			return false;
		}

		return true;
	}



	/**
	 * @{inheritdoc}
	 */
	public function autoRotate(string $path, array $info): array
	{
		$image = new \Imagick();
		$image->readImage($path);

		$orientation = $image->getImageOrientation();

		switch ($orientation) {
			case \Imagick::ORIENTATION_TOPRIGHT:
				$image->flopImage();
				break;
			case \Imagick::ORIENTATION_BOTTOMRIGHT:
				$image->rotateImage(new \ImagickPixel(), 180);
				break;
			case \Imagick::ORIENTATION_BOTTOMLEFT:
				$image->flopImage();
				$image->rotateImage(new \ImagickPixel(), 180);
				break;
			case \Imagick::ORIENTATION_LEFTTOP:
				$image->flopImage();
				$image->rotateImage(new \ImagickPixel(), -90);
				break;
			case \Imagick::ORIENTATION_RIGHTTOP:
				$image->rotateImage(new \ImagickPixel(), 90);
				break;
			case \Imagick::ORIENTATION_RIGHTBOTTOM:
				$image->flopImage();
				$image->rotateImage(new \ImagickPixel(), 90);
				break;
			case \Imagick::ORIENTATION_LEFTBOTTOM:
				$image->rotateImage(new \ImagickPixel(), -90);
				break;
		}

		$image->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
		$image->writeImage($path);

		$dimensions = [
			'width'  => $image->getImageWidth(),
			'height' => $image->getImageHeight()
		];

		$image->clear();
		$image->destroy();

		return $dimensions;
	}
}
