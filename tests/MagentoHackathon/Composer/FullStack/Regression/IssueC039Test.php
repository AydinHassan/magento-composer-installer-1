<?php

namespace MagentoHackathon\Composer\Magento\Regression;

use Cotya\ComposerTestFramework;

class IssueC039Test extends ComposerTestFramework\PHPUnit\FullStackTestCase
{

    /**
     * @return \SplFileInfo
     */
    protected function getArtifactDir()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return new \SplFileInfo("C:/ComposerTests/artifact");
        }

        return new \SplFileInfo(sprintf('%s/ComposerTests/artifact', sys_get_temp_dir()));
    }

    /**
     * @group regression
     */
    public function testAddAndRemoveSymlinkedModule()
    {
        $composer = new ComposerTestFramework\Composer\Wrapper();
        $projectDirectory = new \SplFileInfo(self::getTempComposerProjectPath());

        $testFilePath = $projectDirectory->getPathname().
            '/build/app/design/frontend/test/default/updateFileRemove/design/test1.phtml';

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
                'magento-hackathon/magento-composer-installer' => '999.0.0',
                'magento-hackathon/magento-composer-installer-test-updateFileRemove' => '1.0.0'
            ],
            'extra' => [
                'magento-deploysttrategy' => 'symlink',
                'magento-root-dir' => './build'
            ]
        ];

        $composerJson->fwrite(json_encode($json, JSON_PRETTY_PRINT));
        $composer->install($projectDirectory, $composerJson);
        $this->assertFileExists($testFilePath);



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
                'magento-hackathon/magento-composer-installer' => '*',
            ],
            'extra' => [
                'magento-deploysttrategy' => 'symlink',
                'magento-root-dir' => './build'
            ]
        ];

        $composerJson->fwrite(json_encode($json, JSON_PRETTY_PRINT));
        $composer->update($projectDirectory, $composerJson);

        $this->assertFileNotExists($testFilePath);
        $this->assertFalse(is_link($testFilePath), 'There is still a link');
    }
}
