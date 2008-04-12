<?php
/**
 * package.xml generation script
 * @package Services_Eniro
 * @author  Lars Olesen <lars@legestue.net>
 * @version 0.1.0
 */
require_once 'PEAR/PackageFileManager2.php';

$version = '0.1.6';
$stability = 'alpha';
$notes = '
* Updated parser to new eniro outline
* Made construct parameter optional at it is not used
';

PEAR::setErrorHandling(PEAR_ERROR_DIE);
$pfm = new PEAR_PackageFileManager2();
$pfm->setOptions(
    array('baseinstalldir'    => '/',
          'filelistgenerator' => 'file',
          'packagedirectory'  => dirname(__FILE__).'/src/',
          'packagefile'       => 'package.xml',
          'ignore'            => array('generate_package_xml.php',
                                       '*.tgz'),
          'exceptions'        => array(),
          'simpleoutput'      => true,
    )
);

$pfm->setPackage('Services_Eniro');
$pfm->setSummary('Request an address using a phone number from Eniro');
$pfm->setDescription('Uses HTTP_Request to communicate with Eniro to get an address when supplied with a phone number.');
$pfm->setChannel('public.intraface.dk');
$pfm->setLicense('BSD license', 'http://www.opensource.org/licenses/bsd-license.php');
$pfm->addMaintainer('lead', 'lsolesen', 'Lars Olesen', 'lars@legestue.net');

$pfm->setPackageType('php');

$pfm->setAPIVersion($version);
$pfm->setReleaseVersion($version);
$pfm->setAPIStability($stability);
$pfm->setReleaseStability($stability);
$pfm->setNotes($notes);
$pfm->addRelease();

$pfm->addGlobalReplacement('package-info', '@package-version@', 'version');

$pfm->clearDeps();
$pfm->setPhpDep('5.1.0');
$pfm->setPearinstallerDep('1.5.0');
$pfm->addPackageDepWithChannel('required', 'HTTP_Request', 'pear.php.net', '1.0.0');

$pfm->generateContents();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    if ($pfm->writePackageFile()) {
        exit('package file written');
    }
} else {
    $pfm->debugPackageFile();
}
?>