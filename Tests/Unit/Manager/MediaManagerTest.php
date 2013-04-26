<?php

namespace Pim\Bundle\ProductBundle\Tests\Unit\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Pim\Bundle\ProductBundle\Manager\MediaManager;

/**
 * Test related class
 *
 * @author    Gildas Quemener <gildas.quemener@gmail.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MediaManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itShouldUploadIfAFileIsPresent()
    {
        $filesystem   = $this->getFilesystemMock();
        $target       = $this->getTargetedClass($filesystem);
        $media        = $this->getMediaMock($this->getFileMock());

        $filesystem->expects($this->once())
                   ->method('write')
                   ->with(
                       $this->equalTo('foo.jpg'),
                       $this->anything(),
                       $this->equalTo(false)
                   );

        $media->expects($this->once())
              ->method('setOriginalFilename')
              ->with($this->equalTo('akeneo.jpg'));

        $media->expects($this->once())
              ->method('setFilename')
              ->with($this->equalTo('foo.jpg'));

        $media->expects($this->any())
              ->method('getFilename')
              ->will($this->returnValue('foo.jpg'));

        $media->expects($this->once())
              ->method('setFilepath')
              ->with($this->equalTo('/tmp/upload/foo.jpg'));

        $media->expects($this->once())
              ->method('setMimeType')
              ->with($this->equalTo('image/jpeg'));

        $target->handle($media, 'foo.jpg');
    }

    private function getTargetedClass($filesystem)
    {
        $name = 'foo';
        $filesystemMap = $this->getFilesystemMapMock($name, $filesystem);

        return new MediaManager(
            $filesystemMap, $name, '/tmp/upload'
        );
    }

    private function getFilesystemMapMock($name, $filesystem)
    {
        $filesystemMap = $this
            ->getMockBuilder('Knp\Bundle\GaufretteBundle\FilesystemMap')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock()
        ;

        $filesystemMap->expects($this->any())
                      ->method('get')
                      ->with($this->equalTo($name))
                      ->will($this->returnValue($filesystem));

        return $filesystemMap;
    }

    private function getFilesystemMock()
    {
        $filesystem = $this
            ->getMockBuilder('Knp\Bundle\GaufretteBundle\Filesystem')
            ->setMethods(array('write', 'has'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $filesystem->expects($this->any())
                   ->method('has')
                   ->will($this->returnValue(true));

        return $filesystem;
    }

    private function getMediaMock($file = null)
    {
        $media = $this
            ->getMockBuilder('Oro\Bundle\FlexibleEntityBundle\Entity\Media')
            ->setMethods(array('getFile', 'setOriginalFilename', 'setFilename', 'getFilename', 'setFilepath', 'setMimeType'))
            ->getMock()
        ;

        $media->expects($this->any())
              ->method('getFile')
              ->will($this->returnValue($file));

        return $media;
    }

    private function getFileMock()
    {
        $file = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->setMethods(array('getPathname', 'getClientOriginalName', 'getMimeType'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $file->expects($this->any())
             ->method('getPathname')
             ->will($this->returnValue('src/Pim/Bundle/ProductBundle/Tests/fixtures/akeneo.jpg'));

        $file->expects($this->any())
             ->method('getClientOriginalName')
             ->will($this->returnValue('akeneo.jpg'));

        $file->expects($this->any())
             ->method('getMimeType')
             ->will($this->returnValue('image/jpeg'));

        return $file;
    }
}
