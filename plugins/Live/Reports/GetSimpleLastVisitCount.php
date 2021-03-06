<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\Live\Reports;

use Piwik\Config;
use Piwik\Metrics\Formatter;
use Piwik\Piwik;
use Piwik\Plugin\Report;
use Piwik\Plugins\Live\Controller;
use Piwik\Plugins\Live\VisitorLog;
use Piwik\API\Request;
use Piwik\View;

class GetSimpleLastVisitCount extends Base
{
    protected function init()
    {
        parent::init();
        $this->widgetTitle = 'Live_RealTimeVisitorCount';
        $this->order = 3;
    }

    public function render()
    {
        $lastMinutes = Config::getInstance()->General[Controller::SIMPLE_VISIT_COUNT_WIDGET_LAST_MINUTES_CONFIG_KEY];

        $params    = array('lastMinutes' => $lastMinutes, 'showColumns' => array('visits', 'visitors', 'actions'));
        $lastNData = Request::processRequest('Live.getCounters', $params);

        $formatter = new Formatter();

        $view = new View('@Live/getSimpleLastVisitCount');
        $view->lastMinutes = $lastMinutes;
        $view->visitors    = $formatter->getPrettyNumber($lastNData[0]['visitors']);
        $view->visits      = $formatter->getPrettyNumber($lastNData[0]['visits']);
        $view->actions     = $formatter->getPrettyNumber($lastNData[0]['actions']);
        $view->refreshAfterXSecs = Config::getInstance()->General['live_widget_refresh_after_seconds'];
        $view->translations = array(
            'one_visitor' => Piwik::translate('Live_NbVisitor'),
            'visitors'    => Piwik::translate('Live_NbVisitors'),
            'one_visit'   => Piwik::translate('Intl_OneVisit'),
            'visits'      => Piwik::translate('General_NVisits'),
            'one_action'  => Piwik::translate('General_OneAction'),
            'actions'     => Piwik::translate('VisitsSummary_NbActionsDescription'),
            'one_minute'  => Piwik::translate('Intl_OneMinute'),
            'minutes'     => Piwik::translate('Intl_NMinutes')
        );

        return $view->render();
    }
}