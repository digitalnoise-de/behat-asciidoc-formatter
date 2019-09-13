<?php
declare(strict_types=1);

namespace Digitalnoise\Behat\AsciiDocFormatter\Tests\Output;

use Digitalnoise\Behat\AsciiDocFormatter\Output\OutputDirectoryResetter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;
use PHPUnit\Framework\TestCase;

/**
 * @author Philip Weinke <philip.weinke@digitalnoise.de>
 */
class OutputDirectoryResetterTest extends TestCase
{
    private const OUTPUT_DIRECTORY = 'reports';
    private const THEMES_DIRECTORY = 'themes';

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    /**
     * @var OutputDirectoryResetter
     */
    private $resetter;

    public function test_themes_should_be_copied_to_directory()
    {
        $themeStructure = ['behat-theme.yml' => 'theme-content'];
        vfsStream::create($themeStructure, $this->root->getChild(self::THEMES_DIRECTORY));

        $this->resetter->reset($this->root->getChild(self::OUTPUT_DIRECTORY)->url());

        self::assertEquals(
            ['reports' => ['themes' => $themeStructure]],
            vfsStream::inspect(new vfsStreamStructureVisitor(), $this->root->getChild(self::OUTPUT_DIRECTORY))
                ->getStructure()
        );
    }

    public function test_clear_should_remove_any_files_in_directory()
    {
        $structure = [
            'index.adoc' => '',
            'suites'     => [
                'default' => [
                    'feature.adoc' => '',
                ],
            ],
            'themes'     => [
                'behat-theme.yml'  => 'old-theme',
                'custom-theme.yml' => '',
            ],
        ];
        vfsStream::create($structure, $this->root->getChild(self::OUTPUT_DIRECTORY));

        $this->resetter->reset($this->root->getChild(self::OUTPUT_DIRECTORY)->url());

        self::assertEquals(
            ['reports' => ['themes' => []]],
            vfsStream::inspect(new vfsStreamStructureVisitor(), $this->root->getChild(self::OUTPUT_DIRECTORY))
                ->getStructure()
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->root     = vfsStream::setup('root', null, [self::OUTPUT_DIRECTORY => [], self::THEMES_DIRECTORY => []]);
        $this->resetter = new OutputDirectoryResetter($this->root->getChild(self::THEMES_DIRECTORY)->url());
    }
}
