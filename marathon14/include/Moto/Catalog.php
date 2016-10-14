<?php
if (!defined('DEBUG_MODE'))
    define('DEBUG_MODE', false);

include_once 'Moto/Catalog/Template.php';

class Moto_Catalog
{
    protected $_db;
    protected $_tableNames = array(
        'templates' => 'templates',
        'screenshots' => 'screenshots',
        'templateauthors' => 'templateauthors',
        'templatetypes' => 'templatetypes',
        'templatedescriptions' => 'templatedescriptions',
        'templatecategories' => 'templatecategories',
        'templatekeywords' => 'templatekeywords',
        'templatesoftwares' => 'templatesoftwares',
        'templatesources' => 'templatesources',
        'templatestyles' => 'templatestyles',
        'templatepackages' => 'templatepackages',
        'subsections' => 'subsections',
        'templatecategories_templates' => 'templatecategories_templates',
        'templatekeywords_templates' => 'templatekeywords_templates',
        'templates_templatesoftwares' => 'templates_templatesoftwares',
        'templates_templatesources' => 'templates_templatesources',
        'templates_templatestyles' => 'templates_templatestyles',
        'templatesubsections' => 'templatesubsections',
        'screenshots_templatesubsections' => 'screenshots_templatesubsections'
    );

    protected $_cols;
    protected $_rows;
    protected $_tablePrefix = '';
    protected $_useMatrixOutput = true;
    protected $_ignores = array();
    protected $_joinedCategories = false;

    function __construct($rows = 2, $cols = 2)
    {

        //$this->_db = Database::instance('default', array('table_prefix' => $this->_tablePrefix));
        $this->_db = Database::instance();
        //TODO:: check prefix

        $this->setOutputFormat($rows, $cols);
    }

    function setOutputFormat($rows = 2, $cols = 2)
    {
	    $this->_useMatrixOutput = !($rows == 'flat');

	    if (!$this->_useMatrixOutput)
	        return;

        $cols = abs(intval($cols));
        $rows = abs(intval($rows));

        $this->_cols = $cols ? $cols : 1;
        $this->_rows = $rows ? $rows : 1;
    }

    /**
     * Takes IDs of categories and types to exclude them from selected records.
     * @param $ignores
     * @return mixed
     */
    function ignore($ignores)
    {
        $sections = array('types', 'categories');
        if (!is_array($ignores))
            return;

        foreach($sections as $section)
        {
            if (isset($ignores[$section]))
            {
                $this->_ignores[$section] = is_array($ignores[$section]) ? $ignores[$section] : array($ignores[$section]);
            }
        }
    }

    function clearIgnores()
    {
        $this->_ignores = array();
    }

    function __call($name, $args = array())
    {
        $name = '_' . $name . 'Action';
        if (method_exists($this, $name) )
        {
            $args = is_array($args) ? $args : array($args);
            return call_user_func_array(array($this, $name), $args);
        }
        else
        {
            throw new Exception('Method ' . $name . ' not exists');
        }
    }

    protected function _joinCategories($alias, Database_Core $query)
    {
        if (!$this->_joinedCategories)
        {
            $query->join($this->_tableNames['templatecategories_templates'] . ' as templates_categories', $alias . '.id', 'templates_categories.template_id')
                ->join($this->_tableNames['templatecategories'] . ' as categories', 'templates_categories.templatecategory_id', 'categories.id');
            $query->select('categories.name as templatecategory');
            $query->select('templates_categories.templatecategory_id');
            $this->_skipHiddenCategories('categories', $query);
            $this->_joinedCategories = true;
        }
    }

    protected function _addCategoriesFilter($alias, $categories = array(), Database_Core $query)
    {
        if (empty($categories))
            return;
        $categories = is_array($categories) ? $categories : array($categories);

        if ($alias == 'templates')
        {
            $this->_joinCategories($alias, $query);

            if(!empty($categories))
            {
                $query->in('templates_categories.templatecategory_id', $categories);
            }
        }

    }

    protected function _addTypesFilter($alias, $types = array(), Database_Core $query)
    {
        if (empty($types))
            return;
        $types = is_array($types) ? $types : array($types);

        if ($alias == 'templates')
        {
            $query->join($this->_tableNames['templatetypes'] . ' as templatetypes', $alias . '.templatetype_id', 'templatetypes.id');

            if(!empty($types))
            {
                $query->in($alias . '.templatetype_id', $types);
            }

            $query->select('templatetypes.name as templatetype');
            $query->select($alias . '.templatetype_id');


        }

    }

    protected function _addFeaturedFilter($alias, $downloadCount, Database_Core $query)
    {
        $downloadCount = abs(intval($downloadCount));
        if (!$downloadCount)
            return;

        if ($alias == 'templates')
        {
            $query->select($alias . '.download_count');
	        $query->orderby($alias . '.download_count', 'DESC');
            $query->condition($alias . '.download_count >= ' . $downloadCount);
        }

    }

    protected function _getTemplateBuyUrl($templateId)
    {
        $params = array('act' => 'add', 'templ' => $templateId, 'pr_code' => Setting_Model::get(Setting_Model::S_AFF_CODE),);
        if (Zend_Registry::isRegistered('affiliate-program')) {
            $affiliate_program = Zend_Registry::get('affiliate-program');
            if ($affiliate_program->getActive()) {
                $params ['referal'] = $affiliate_program->getAffiliateId();
            }
        }
        $buy_url = 'http://www.templatehelp.com/preset/cart.php?' . http_build_query($params);
        return $buy_url;
    }

    protected function _skipHiddenCategories($alias, Database_Core $query)
    {
        $query->condition($alias . '.visibility = 1');
        $query->condition($alias . '.enabled_template_count > 0');
    }

    protected function _formatQuery($alias = 'templates', $pageNum = 0, $filters = array())
    {
        $this->_joinedCategories = false;
        $pageNum = abs($pageNum);

        $db = $this->_db;
        $res = false;

        $query = $db->from ($this->_tableNames[$alias] . ' as ' . $alias);

        switch($alias)
        {
            case 'templates':
                $query->select($alias . '.id', $alias . '.price', $alias . '.discounted_price', $alias . '.templatepackage_id', 'templatepackages.name');
                if(DEBUG_MODE)
                {
                    $this->_joinCategories($alias, $query);
                }
				
                $query->join($this->_tableNames['templatepackages'] . ' as templatepackages', $alias . '.templatepackage_id', 'templatepackages.id');
                $query->where('templatepackages.visibility', 1);

                // categories filter
                if (isset($filters['categories']))
                {
                    $this->_addCategoriesFilter($alias, $filters['categories'], $query);
                }

                // types filter
                if (isset($filters['types']))
                {
                    $this->_addTypesFilter($alias, $filters['types'], $query);
                }

                // types filter
                if (isset($filters['featured']))
                {
                    $this->_addFeaturedFilter($alias, $filters['featured'], $query);
                }

                // apply type ignores
                if (isset($this->_ignores['types']))
                {
                    $query->notin($alias . '.templatetype_id', $this->_ignores['types']);
                }

                // apply categories ignores
                if (isset($this->_ignores['categories']))
                {
                    $this->_joinCategories($alias, $query);
                    $query->notin('templates_categories.templatecategory_id', $this->_ignores['categories']);
                }

                $query->orderby($alias . '.inserted_date', 'DESC');
                // add additional filters here...
                $query->where($alias . '.disabled', 0);

                if (Moto_Config::get('hidden_templates'))
                {
                    $query->notin($alias . '.id', Moto_Config::get('hidden_templates'));
                }
                break;
            case 'templatecategories':
				$query->orderby($alias . '.list_name', 'ASC');
                $query->select($alias . '.id', $alias . '.name',  $alias . '.html_name',  $alias . '.list_name', $alias . '.url_name', $alias . '.template_count');
                $this->_skipHiddenCategories($alias, $query);
                // apply categories ignores
                if (isset($this->_ignores['categories']))
                {
                    $query->notin($alias . '.id', $this->_ignores['categories']);
                }
                break;
            case 'templatetypes':
                $query->orderby($alias . '.template_count', 'DESC');
                $query->select($alias . '.id', $alias . '.name',  $alias . '.html_name',  $alias . '.list_name', $alias . '.url_name', $alias . '.template_count');

                // apply types ignores
                if (isset($this->_ignores['types']))
                {
                    $query->notin($alias . '.id', $this->_ignores['types']);
                }
                break;
        }
        if ($pageNum)
        {
            $queryNumRows = clone $query;
            $limit = $this->_cols * $this->_rows;
            $offset = abs(($pageNum - 1) * $limit);
            $query->limit($limit, $offset);

            $total_rows = $db->query ('SELECT COUNT(*) as total_rows FROM ('. str_replace('SQL_CALC_FOUND_ROWS', ' ', $queryNumRows->compile()) . ') as r')->as_array();
            $total_rows = $total_rows[0]->total_rows;
        }
        try
        {
            $compiled = $query->compile();
            $res = $db->query($compiled)->as_array();

            // add buy url for templates
            if ($alias == 'templates')
            {
                foreach($res as $index => $template)
                {
                    $template->buyUrl = $this->_getTemplateBuyUrl($template->id);
                    if (DEBUG_MODE)
                    {
                        $res[$index] = new Moto_Catalog_Template((array)$template);
                    }
                }
            }

            if ($pageNum)
            {
                $res = array(
                    'data' => $this->_useMatrixOutput ? $this->_formatOutputData($res) : $res,
                    'totalRows' => $total_rows,
                    'pageNum' => $pageNum
                );
            }
        }
        catch(Exception $e)
        {
            echo($e->getMessage());
        }

        return $res;
    }

    protected function _formatOutputData($data)
    {
        if (!is_array($data))
            return;

        $res = array();
        $counter = 0;
        for ($i = 0; $i < $this->_rows; $i++)
        {
	        if (!isset($data[$counter]))
		        break;
            $res[$i] = array();
            for ($j = 0; $j < $this->_cols; $j++)
            {
	            if (!isset($data[$counter]))
		            break;
                $res[$i][$j] = $data[$counter];
                $counter++;
            }
        }
        return $res;
    }

    protected function _getTemplatesByCategoriesAction($categories = array(), $pageNum = 0)
    {
        return $this->_formatQuery('templates', $pageNum, array(
            'categories' => $categories
        ));
    }

    protected function _getTemplatesByTypesAction($types = array(), $pageNum = 0)
    {
        return $this->_formatQuery('templates', $pageNum, array(
            'types' => $types
        ));
    }

    protected function _getFeaturedTemplatesAction($downloadCount = 5, $pageNum = 0)
    {
        $data = array(
            'featured' => $downloadCount
        );

        if (func_num_args() == 3)
        {
            $data['categories'] = func_get_args(2);
        }

        return $this->_formatQuery('templates', $pageNum, $data);
    }

    protected function _getTemplatesAction($pageNum = 0)
    {
        return $this->_formatQuery('templates', $pageNum);
    }

    protected function _getCategoriesAction($pageNum = 0)
    {
        return $this->_formatQuery('templatecategories', $pageNum);
    }

    protected function _getTypesAction($pageNum = 0)
    {
        return $this->_formatQuery('templatetypes', $pageNum);
    }
}
