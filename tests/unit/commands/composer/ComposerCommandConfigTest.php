<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;

/**
 * @covers PharIo\Phive\ComposerCommandConfig
 */
class ComposerCommandConfigTest extends \PHPUnit_Framework_TestCase {

    use ScalarTestDataProvider;

    public function testGetTargetDirectory() {
        $directory = $this->getDirectoryMock();

        $locator = $this->getTargetDirectoryLocatorMock();
        $locator->method('getTargetDirectory')->willReturn($directory);

        $commandConfig = new ComposerCommandConfig(
            $this->getOptionsMock(),
            $this->getPhiveXmlConfigMock(),
            $locator,
            $directory
        );

        $this->assertSame($directory, $commandConfig->getTargetDirectory());
    }

    /**
     * @dataProvider boolProvider
     *
     * @param bool $value
     */
    public function testInstallGlobally($value) {
        $options = $this->getOptionsMock();
        $options->method('hasOption')->with('global')->willReturn($value);

        $commandConfig = new ComposerCommandConfig(
            $options,
            $this->getPhiveXmlConfigMock(),
            $this->getTargetDirectoryLocatorMock(),
            $this->getDirectoryMock()
        );

        $this->assertSame($value, $commandConfig->installGlobally());
    }

    /**
     * @dataProvider makeCopyProvider
     *
     * @param bool $hasCopyOption
     * @param bool $hasGlobalOption
     * @param bool $expected
     */
    public function testMakeCopy($hasCopyOption, $hasGlobalOption, $expected) {
        $options = $this->getOptionsMock();
        $options->method('hasOption')->willReturnMap(
            [
                ['copy', $hasCopyOption],
                ['global', $hasGlobalOption]
            ]
        );

        $commandConfig = new ComposerCommandConfig(
            $options,
            $this->getPhiveXmlConfigMock(),
            $this->getTargetDirectoryLocatorMock(),
            $this->getDirectoryMock()
        );

        $this->assertSame($expected, $commandConfig->makeCopy());
    }

    /**
     * @dataProvider doNotAddToPhiveXmlProvider
     *
     * @param bool $temporaryValue
     * @param bool $globalValue
     * @param bool $expected
     */
    public function testDoNotAddToPhiveXml($temporaryValue, $globalValue, $expected) {
        $options = $this->getOptionsMock();
        $options->method('hasOption')->willReturnMap(
            [
                ['temporary', $temporaryValue],
                ['global', $globalValue]
            ]
        );

        $commandConfig = new ComposerCommandConfig(
            $options,
            $this->getPhiveXmlConfigMock(),
            $this->getTargetDirectoryLocatorMock(),
            $this->getDirectoryMock()
        );

        $this->assertSame($expected, $commandConfig->doNotAddToPhiveXml());
    }

    public function testGetComposerFilename() {
        $composerFilename = new Filename('/foo/composer.json');

        $directory = $this->getDirectoryMock();
        $directory->method('file')->with('composer.json')->willReturn($composerFilename);

        $commandConfig = new ComposerCommandConfig(
            $this->getOptionsMock(),
            $this->getPhiveXmlConfigMock(),
            $this->getTargetDirectoryLocatorMock(),
            $directory
        );

        $this->assertSame($composerFilename, $commandConfig->getComposerFilename());
    }

    /**
     * @return array
     */
    public static function doNotAddToPhiveXmlProvider() {
        return [
            [true, false, true],
            [true, true, true],
            [false, true, true],
            [false, false, false],
        ];
    }

    /**
     * @return array
     */
    public static function makeCopyProvider() {
        return [
            [true, false, true],
            [false, false, false],
            [false, true, true]
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Options
     */
    private function getOptionsMock() {
        return $this->createMock(Options::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PhiveXmlConfig
     */
    private function getPhiveXmlConfigMock() {
        return $this->createMock(PhiveXmlConfig::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TargetDirectoryLocator
     */
    private function getTargetDirectoryLocatorMock() {
        return $this->createMock(TargetDirectoryLocator::class);
    }

}
