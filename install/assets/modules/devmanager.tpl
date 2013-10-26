// <?php 
/**
 * DevManager
 * 
 * Быстрая разработка MODX
 * 
 * @category	module
 * @version 	0.3
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal	@properties
 * @internal	@guid devman3405824jf54f452f5tt245t5t
 * @internal	@shareparams 1
 * @internal	@dependencies requires files located at /assets/modules/docmanager/
 * @internal	@modx_category Manager and Admin
 * @internal    @installset base, sample
 */

include_once($modx->config['base_path'].'assets/modules/devmanager/'."developerManager.php");
if (class_exists('DeveloperManager')){
        $manager = new DeveloperManager($modx, $_POST);
        $manager->init();
}
