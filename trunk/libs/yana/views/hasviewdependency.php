<?php

/**
 * YANA library
 *
 * Software:  Yana PHP-Framework
 * Version:   {VERSION} - {DATE}
 * License:   GNU GPL  http://www.gnu.org/licenses/
 *
 * This program: can be redistributed and/or modified under the
 * terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 *
 * This notice MAY NOT be removed.
 *
 * @package  yana
 * @license  http://www.gnu.org/licenses/gpl.txt
 *
 * @ignore
 */
declare(strict_types=1);

namespace Yana\Views;

/**
 * <<trait>> injects dependency.
 *
 * @package     yana
 * @subpackage  views
 * @codeCoverageIgnore
 */
trait HasViewDependency
{

    /**
     * @var  \Yana\Core\Dependencies\IsViewContainer
     */
    private $_dependencyContainer = null;

    /**
     * <<construct>> Initialize dependencies.
     *
     * @param  \Yana\Core\Dependencies\IsViewContainer  $container  dependency container
     */
    public function __construct(\Yana\Core\Dependencies\IsViewContainer $container)
    {
        $this->_dependencyContainer = $container;
    }

    /**
     * Returns a reference to the registered view manager.
     *
     * You may use this to modify settings of the view layer and access the template engine.
     *
     * @return  \Yana\Core\Dependencies\IsViewContainer
     */
    protected function _getDependencyContainer(): \Yana\Core\Dependencies\IsViewContainer
    {
        return $this->_dependencyContainer;
    }

    /**
     * Returns a reference to the registered view manager.
     *
     * You may use this to modify settings of the view layer and access the template engine.
     *
     * @return  \Yana\Views\Managers\IsSmartyManager
     */
    protected function _getViewManager(): \Yana\Views\Managers\IsSmartyManager
    {
        return $this->_getDependencyContainer()->getView();
    }

    /**
     * Returns the application's default icon loader.
     *
     * @return  \Yana\Views\Icons\IsLoader
     */
    protected function _getIconLoader(): \Yana\Views\Icons\IsLoader
    {
        return $this->_getDependencyContainer()->getIconLoader();
    }

    /**
     * Creates and returns an application menu builder.
     *
     * @return  \Yana\Plugins\Menus\IsCacheableBuilder
     */
    protected function _getMenuBuilder(): \Yana\Plugins\Menus\IsCacheableBuilder
    {
        return $this->_getDependencyContainer()->getMenuBuilder();
    }

    /**
     * Get language translation-repository.
     *
     * This returns the language component. If none exists, a new instance is created.
     *
     * @return  \Yana\Translations\IsFacade
     */
    protected function _getLanguage(): \Yana\Translations\IsFacade
    {
        return $this->_getDependencyContainer()->getLanguage();
    }

    /**
     * Returns the attached logger.
     *
     * @return  \Yana\Log\IsLogHandler
     */
    protected function _getLogger(): \Yana\Log\IsLogHandler
    {
        return $this->_getDependencyContainer()->getLogger();
    }

    /**
     * Get registry.
     *
     * This returns the registry. If none exists, a new instance is created.
     *
     * @return  \Yana\VDrive\IsRegistry
     * @throws  \Yana\Core\Exceptions\NotReadableException    when Registry file is not readable
     * @throws  \Yana\Core\Exceptions\InvalidSyntaxException  when Registry file could not be read or contains invalid syntax
     */
    protected function _getRegistry(): \Yana\VDrive\IsRegistry
    {
        return $this->_getDependencyContainer()->getRegistry();
    }

    /**
     * Get current profile id.
     *
     * Returns the id of the profile the data of the current profile is to be associated with.
     *
     * @return  string
     */
    protected function _getProfileId(): string
    {
        return $this->_getDependencyContainer()->getProfileId();
    }

    /**
     * Get skin.
     *
     * This returns the skin component. If none exists, a new instance is created.
     *
     * @return  \Yana\Views\Skins\IsSkin
     */
    protected function _getSkin(): \Yana\Views\Skins\IsSkin
    {
        return $this->_getDependencyContainer()->getSkin();
    }

}

?>