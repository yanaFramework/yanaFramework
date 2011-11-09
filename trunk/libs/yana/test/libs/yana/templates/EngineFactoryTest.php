<?php

namespace Yana\Templates;

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/../../../../templates/enginefactory.php';

/**
 * @ignore
 */
class MyTestPlugin
{
    public function __invoke()
    {
        return "test";
    }
}

/**
 * Test class for EngineFactory.
 * Generated by PHPUnit on 2011-11-05 at 00:01:10.
 */
class EngineFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var EngineFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $config = simplexml_load_string('<templates>
        <leftdelimiter>{</leftdelimiter>
        <rightdelimiter>}</rightdelimiter>
        <templatedir>skins/</templatedir>
        <templatedir>plugins/</templatedir>
        <cachedir>cache/</cachedir>
        <caching>false</caching>
        <cachelifetime></cachelifetime>
        <cachingtype>file</cachingtype>
        <compilecheck>true</compilecheck>
        <compiledir>cache/</compiledir>
        <configdir>skins/.config/</configdir>
        <debugging>false</debugging>
        <defaultmodifier>replaceToken</defaultmodifier>
        <resourcetype name="id">\Yana\Templates\Resources\IdResource</resourcetype>
        <resourcetype name="template">\Yana\Templates\Resources\FileResource</resourcetype>
        <resourcetype name="string">\Yana\Templates\Resources\StringResource</resourcetype>
        <modifier name="test1" cacheable="true">\Yana\Templates\MyTestPlugin</modifier>
        <function name="test2" cacheable="true">\Yana\Templates\MyTestPlugin</function>
        <function name="test3" cacheable="false">\Yana\Templates\MyTestPlugin</function>
        <blockfunction name="test4" cacheable="true">\Yana\Templates\MyTestPlugin</blockfunction>
        <prefilter>\Yana\Templates\Helpers\PreFilters\LanguageTokenFilter</prefilter>
        <postfilter>\Yana\Templates\Helpers\PostFilters\SpamFilter</postfilter>
        <outputfilter>\Yana\Templates\Helpers\OutputFilters\RssFilter</outputfilter>
        <defaultresourcetype>template</defaultresourcetype>
        <usesubdirs>true</usesubdirs>
        <security>
            <phphandling>remove</phphandling>
            <securedir></securedir>
            <trusteddir></trusteddir>
            <staticclass>false</staticclass>
            <phpfunction>isset</phpfunction>
            <phpfunction>empty</phpfunction>
            <phpfunction>count</phpfunction>
            <phpfunction>sizeof</phpfunction>
            <phpfunction>in_array</phpfunction>
            <phpfunction>is_array</phpfunction>
            <phpfunction>time</phpfunction>
            <phpfunction>nl2br</phpfunction>
            <phpmodifier>escape</phpmodifier>
            <phpmodifier>count</phpmodifier>
            <streams>false</streams>
            <allowedmodifier></allowedmodifier>
            <disabledmodifier></disabledmodifier>
            <allowedtag></allowedtag>
            <disabledtag></disabledtag>
            <allowconstants>false</allowconstants>
            <allowsuperglobals>false</allowsuperglobals>
            <allowphptag>false</allowphptag>
        </security>
    </templates>');
        $this->object = new EngineFactory($config);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * Must not raise an error or throw an exception.
     *
     * @test
     */
    public function testCreateInstance()
    {
        $smarty = $this->object->createInstance();
    }

}

?>
