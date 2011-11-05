<?php

namespace Yana\Templates;

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/../../../../templates/enginefactory.php';

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
        <modifier name="embeddedTags" cacheable="true">\Yana\Templates\Helpers\Modifiers\EmbeddedTags</modifier>
        <modifier name="replaceToken" cacheable="false">\Yana\Templates\Helpers\Modifiers\ReplaceToken</modifier>
        <modifier name="css" cacheable="true">\Yana\Templates\Helpers\Modifiers\Css</modifier>
        <modifier name="date" cacheable="true">\Yana\Templates\Helpers\Modifiers\Date</modifier>
        <modifier name="entities" cacheable="true">\Yana\Templates\Helpers\Modifiers\Entities</modifier>
        <modifier name="href" cacheable="false">\Yana\Templates\Helpers\Modifiers\Href</modifier>
        <modifier name="scanForAt" cacheable="true">\Yana\Templates\Helpers\Modifiers\ScanForAt</modifier>
        <modifier name="smilies" cacheable="true">\Yana\Templates\Helpers\Modifiers\Smilies</modifier>
        <modifier name="url" cacheable="false">\Yana\Templates\Helpers\Modifiers\Url</modifier>
        <modifier name="urlEncode" cacheable="true">\Yana\Templates\Helpers\Modifiers\UrlEncode</modifier>
        <function name="printArray" cacheable="true">\Yana\Templates\Helpers\Functions\PrintArray</function>
        <function name="printUnorderedList" cacheable="true">\Yana\Templates\Helpers\Functions\PrintUnorderedList</function>
        <function name="rss" cacheable="true">\Yana\Templates\Helpers\Functions\Rss</function>
        <function name="import" cacheable="false">\Yana\Templates\Helpers\Functions\Import</function>
        <function name="smilies" cacheable="true">\Yana\Templates\Helpers\Functions\GuiSmilies</function>
        <function name="embeddedTags" cacheable="true">\Yana\Templates\Helpers\Functions\GuiEmbeddedTags</function>
        <function name="create" cacheable="false">\Yana\Templates\Helpers\Functions\CreateForm</function>
        <function name="captcha" cacheable="true">\Yana\Templates\Helpers\Functions\Captcha</function>
        <function name="slider" cacheable="true">\Yana\Templates\Helpers\Functions\Slider</function>
        <function name="sizeOf" cacheable="true">\Yana\Templates\Helpers\Functions\SizeOf</function>
        <function name="toolbar" cacheable="false">\Yana\Templates\Helpers\Functions\Toolbar</function>
        <function name="preview" cacheable="true">\Yana\Templates\Helpers\Functions\Preview</function>
        <function name="colorpicker" cacheable="true">\Yana\Templates\Helpers\Functions\Colorpicker</function>
        <function name="sml_load" cacheable="true">\Yana\Templates\Helpers\Functions\SmlLoad</function>
        <function name="smlLoad" cacheable="true">\Yana\Templates\Helpers\Functions\SmlLoad</function>
        <function name="lang" cacheable="true">\Yana\Templates\Helpers\Functions\Lang</function>
        <function name="visitorCount" cacheable="true">\Yana\Templates\Helpers\Functions\VisitorCount</function>
        <function name="portlet" cacheable="true">\Yana\Templates\Helpers\Functions\Portlet</function>
        <function name="applicationBar" cacheable="true">\Yana\Templates\Helpers\Functions\ApplicationBar</function>
        <function name="selectDate" cacheable="true">\Yana\Templates\Helpers\Functions\SelectDate</function>
        <function name="selectTime" cacheable="true">\Yana\Templates\Helpers\Functions\SelectTime</function>
        <blockfunction name="loop" cacheable="true">\Yana\Templates\Helpers\BlockFunctions\LoopArray</blockfunction>
        <prefilter>\Yana\Templates\Helpers\PreFilters\LanguageTokenFilter</prefilter>
        <prefilter>\Yana\Templates\Helpers\PreFilters\IncludeFilter</prefilter>
        <prefilter>\Yana\Templates\Helpers\PreFilters\AjaxBridgeFilter</prefilter>
        <prefilter>\Yana\Templates\Helpers\PreFilters\RelativePathsFilter</prefilter>
        <postfilter>\Yana\Templates\Helpers\PostFilters\SpamFilter</postfilter>
        <outputfilter>\Yana\Templates\Helpers\OutputFilters\RssFilter</outputfilter>
        <outputfilter>\Yana\Templates\Helpers\OutputFilters\CssFilter</outputfilter>
        <outputfilter>\Yana\Templates\Helpers\OutputFilters\MicrosummaryFilter</outputfilter>
        <outputfilter>\Yana\Templates\Helpers\OutputFilters\JsFilter</outputfilter>
        <outputfilter>\Yana\Templates\Helpers\OutputFilters\LanguageTokenFilter</outputfilter>
        <varfilter></varfilter>
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
     * @todo Implement testCreateInstance().
     */
    public function testCreateInstance()
    {
        $smarty = $this->object->createInstance();
    }

}

?>
