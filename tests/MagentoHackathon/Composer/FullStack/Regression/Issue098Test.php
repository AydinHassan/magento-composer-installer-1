<?php
/**
 *
 *
 *
 *
 */

namespace MagentoHackathon\Composer\Magento\Regression;

use Cotya\ComposerTestFramework;

class Issue098Test extends ComposerTestFramework\PHPUnit\FullStackTestCase
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
    public function testDeployOfEarlierInstalledPackages()
    {
        $composer = new ComposerTestFramework\Composer\Wrapper();
        $projectDirectory = new \SplFileInfo(self::getTempComposerProjectPath());
        $composerJson = new  \SplTempFileObject();
        $json = [
            'repositories' => [
                [
                    'type' => 'artifact',
                    'url' => $this->getArtifactDir()->getRealPath(),
                ]
            ],
            'require' => [
                'magento-hackathon/magento-composer-installer-test-sort1' => '1.0.0',
            ],
            'extra' => [
                'magento-deploysttrategy' => 'copy',
                'magento-force' => 'override',
                'magento-root-dir' => './magento'
            ]
        ];

        $composerJson->fwrite(json_encode($json, JSON_PRETTY_PRINT));

        $composer->install($projectDirectory, $composerJson);

        $this->assertFileNotExists(
            $projectDirectory->getPathname().'/magento/app/design/frontend/test/default/installSort/test1.phtml'
        );

        $composerJson = new  \SplTempFileObject();

        $json = [
            'repositories' => [
                [
                    'type' => 'artifact',
                    'url' => $this->getArtifactDir()->getRealPath(),
                ]
            ],
            'require' => [
                'magento-hackathon/magento-composer-installer-test-sort1' => '1.0.0',
                'magento-hackathon/magento-composer-installer' => '*'
            ],
            'extra' => [
                'magento-deploysttrategy' => 'copy',
                'magento-force' => 'override',
                'magento-root-dir' => './magento'
            ]
        ];

        $composerJson->fwrite(json_encode($json, JSON_PRETTY_PRINT));

        $composer->update($projectDirectory, $composerJson);


        $this->assertFileExists(
            $projectDirectory->getPathname().'/magento/app/design/frontend/test/default/installSort/test1.phtml'
        );
    }
}
