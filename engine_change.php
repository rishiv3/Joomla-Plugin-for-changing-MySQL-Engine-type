<?php

/**
 * @package   Engine Change
 * @author    Rishi Vishwakarma www.rishinc.com
 * @copyright Copyright (C) 2016 www.rishinc.com
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemEngine_change extends JPlugin
{

    public function onAfterInitialise()
    {
        //init vars
        $engine_change = $this->params->get('engine');
        $opposite = $this->getEngineType($engine_change);

        //set database query
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('TABLE_NAME');
        $query->from('INFORMATION_SCHEMA.TABLES');

        $query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '"
                . JFactory::getConfig()->get("db")
                . "' AND ENGINE = '" . $opposite . "'";

        if ($db->setQuery($query))
        {
            $results = $db->loadObjectList();
        }
        else
        {
            return;
        }

        if (!empty($results))
        {
            //cycle through tables and reset the Engine
            foreach ($results as $result)
            {
                $tableName = $result->TABLE_NAME;
                $newQuery = "ALTER TABLE " . $tableName . " ENGINE=" . $engine_change;

                try
                {
                    $db->setQuery($newQuery);
                    $db->execute();
                } catch (Exception $e)
                {
                    /* echo $e->getMessage(); */
                }
            }
        }
    }


    //helps set init vars
    protected function getEngineType($ec) // var $var = engine_change
    {
        if ($ec == 'MyISAM')
        {
            return 'InnoDB';
        }
        else
        {
            return 'MyISAM';
        }
    }

}
