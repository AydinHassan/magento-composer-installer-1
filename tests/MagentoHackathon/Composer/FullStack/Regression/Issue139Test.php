<?php
/**
 *
 *
 *
 *
 */

namespace MagentoHackathon\Composer\Magento\Regression;

use Cotya\ComposerTestFramework;

class Issue139Test extends ComposerTestFramework\PHPUnit\FullStackTestCase
{

    /**
     * @return \SplFileInfo
     */
    protected function getArtifactDir()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return new \SplFileInfo(sprintf("C:/ComposerTests/artifact"));
        }

        return new \SplFileInfo(sprintf('%s/ComposerTests/artifact', sys_get_temp_dir()));
    }

    /**
     * @group regression
     */
    public function testCreateProject()
    {
        $composer = new ComposerTestFramework\Composer\Wrapper();
        $projectDirectory = new \SplFileInfo(self::getTempComposerProjectPath());
        $composerJson = new  \SplTempFileObject();
        $json = [
            'repositories' => [
                [
                    'type' => 'artifact',
                    'url' => $this->getArtifactDir()->getRealPath(),
                ],
                [
                    'type' => 'composer',
                    'url' => 'http://packages.firegento.com'
                ],
            ],
            'require' => [
                'connect20/mage_all_latest' => '*',
                'magento-hackathon/magento-composer-installer' => '*',
                'composer/composer' => '*@dev'
            ],
            'extra' => [
                'magento-deploysttrategy' => 'copy',
                'magento-force' => 'override',
                'magento-root-dir' => './web'
            ]
        ];

        $composerJson->fwrite(json_encode($json, JSON_PRETTY_PRINT));

        $composer->install($projectDirectory, $composerJson);

        $this->assertFileExists($projectDirectory->getPathname().'/web/lib/Varien/Exception.php');
    }
}
