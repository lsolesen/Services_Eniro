<?php
/**
 * @package Services_Eniro
 * @author  Lars Olesen <lars@legestue.net>
 * @license GNU Lesser General Public License (http://www.gnu.org/copyleft/lgpl.html)
 *
 */
require_once 'PHPUnit/Framework.php';
require_once '../src/Services/Eniro.php';

class EniroTest extends PHPUnit_Framework_TestCase
{
    function testQueryReturnsAnArrayWithValidValuesWhenUsingValidPhoneNumberWhenLocalityIsAlsoReturned()
    {
        $eniro = new Services_Eniro();
        $return = $eniro->query('telefon', '98468269');
        $this->assertTrue(is_array($return));
        $this->assertEquals('Doris Kn�sen &  Jens Kn�sen', $return['navn']);
        $this->assertEquals('Gr�svangen 8', $return['adresse']);
        $this->assertEquals('9300', $return['postnr']);
        $this->assertEquals('S�by', $return['postby']);
    }

    function testQueryReturnsAnArrayWithValidValuesWhenUsingValidPhoneNumberWithNoLocality()
    {
        $eniro = new Services_Eniro();
        $return = $eniro->query('telefon', '51906011');
        $this->assertTrue(is_array($return));
        $this->assertEquals('Sune Thorb�ll Jensen', $return['navn']);
        $this->assertEquals('Jens Baggesens Vej 42 3, Th', $return['adresse']);
        $this->assertEquals('8210', $return['postnr']);
        $this->assertEquals('�rhus V', $return['postby']);
    }

    function testSearchWithAString()
    {
        $eniro = new Services_Eniro();
        $return = $eniro->query('telefon', 'dfds');
        $this->markTestIncomplete('what should be done?');
    }




}