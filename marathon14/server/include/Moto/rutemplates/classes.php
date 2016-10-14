<?php

abstract class Rutemplates_DatabaseLayer
{
    protected $_db;
    protected $_tableNames = array(
        'templates' => 'templates',
        'screenshots' => 'screenshots',
        'templateauthors' => 'templateauthors',
        'templatetypes' => 'templatetypes',
        //'templatedescriptions' => 'templatedescriptions',
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
    protected $_tablePrefix = 'rutm_v4';

    function __construct()
    {
        $this->_db = Database::instance();

        // add prefixes
        array_map(create_function('$str','return "'. $this->_tablePrefix .'".$str;'), $this->_tableNames);
    }
}

class Rutemplates_Preview extends Rutemplates_DatabaseLayer
{
    protected $_defaults = array(
        'id' => null,
        'uri' => '42300/42349-m.jpg',
        'width' => 145, // 430
        'height' => 156,
        'position' => 0,
        'screenshot_href_id' => 0,
        'screenshot_description_id' => 28, // or 291
        'is_wide' => 0,
        'nobr' => 0,
        'template_id' => 41146
    );

    protected $_data = array();

    function __construct($templatePreviewData = array())
    {
        parent::__construct();
        $this->_data = array_merge($this->_defaults, $templatePreviewData);
    }

    function save()
    {
        $this->_data['id'] = $this->_generatePreviewId();
        if (VERBOSE)
        {
            echo "<p>Inserting Preview # {$this->_data['id']}</p><pre>";
            var_dump($this->_data);
            echo '</pre>';

        }
        $this->_db->insert($this->_tableNames['screenshots'], $this->_data);

        return $this->_data['id'];
    }

    function getId()
    {
        return $this->_data['id'];
    }

    function setTemplateId($templId)
    {
        $this->_data['template_id'] = $templId;
    }

    protected function _generatePreviewId()
    {
        $id = 0;
        $db = $this->_db;
        $query = $db->select ('MIN(id) as min_id')
            ->from ($this->_tableNames['screenshots'])
            ->compile ();
        $item = $db->query ($query)->as_array();

        if (isset($item[0]))
        {
            if ($item[0]->min_id > 10)
            {
                $id = $item[0]->min_id - 1;
            }

        }
        return $id;
    }
}

class Rutemplates_SmallPreview extends Rutemplates_Preview {}
class Rutemplates_LargePreview extends Rutemplates_Preview {}

class Rutemplates_Template extends Rutemplates_DatabaseLayer
{
    protected $_defaults = array(
        'id' => 0,
        'price' => 139,
        'discounted_price' => 139,
        'exc_price' => 8500,
        'download_count' => 0,
        'is_adult' => 0,
        'templateauthor_id' => 161,
        'templatetype_id' => 68,
        'templatepackage_id' => 254,
        'inserted_date' => 0,
        'updated_date' => 0,
        'small_preview_id' => 0,
        'large_preview_id' => 0,
        'hash' => '',
        'screenshot_download_status' => 0,
        'disabled' => 0
    );


    protected $_id;
    //protected $_description;
    protected $_data = array();
    protected $_smallPreview = array();
    protected $_largePreview = array();
    protected $_subsections = array(3);

    protected $_additionals = array(
        'categories' => array(),
        'keywords' => array(),
        'softwares' => array(53),
        'sources' => array(),
        //'subsections' => array(),
    );

    function __construct($templateData = array())
    {
        parent::__construct();
        $this->_data = array_merge($this->_defaults, $templateData);
        $this->_id = $this->_data['id'];
    }

    function getId()
    {
        return $this->_id;
    }

    function addSmallPreview(Rutemplates_SmallPreview $preview)
    {
        $preview->setTemplateId($this->_id);
        $this->_smallPreview = $preview;
    }

    function addLargePreview(Rutemplates_LargePreview $preview)
    {
        $preview->setTemplateId($this->_id);
        $this->_largePreview = $preview;
    }
    /*
    function addDescription($description)
    {
        $this->_description = $description;
        $this->_description['id'] = $this->_id;
    }
    */

    function additionals($adds)
    {
        $this->_additionals = array_merge($this->_additionals, $adds);
    }

    function save()
    {
        $this->_data['small_preview_id'] = $this->_smallPreview->save();
        $this->_data['large_preview_id'] = $this->_largePreview->save();
        $this->_saveAdditionals();
        $this->_saveSubsections();


        /*
        // insert template description
        if (VERBOSE)
        {
            echo "<p>Inserting description for template # {$this->_description['id']}</p><pre>";
            var_dump($this->_description);
            echo '</pre>';

        }
        $this->_db->insert($this->_tableNames['templatedescriptions'], $this->_description);
        */

        // insert template

        // save current date
        $this->_data['inserted_date'] = date("Y-m-d H:i:s", time());
        if (VERBOSE)
        {
            echo "<p>Inserting template # {$this->_id}</p><pre>";
            var_dump($this->_data);
            echo '</pre>';
        }

        $this->_db->insert($this->_tableNames['templates'], $this->_data);

    }

    function getAuthorId()
    {
        return $this->_data['templateauthor_id'];
    }

    function getCategoryIds()
    {
        return $this->_additionals['categories'];
    }

    protected function _saveAdditionals()
    {
        // for testing only
        $disables = array(
            'categories' => 0,
            'keywords' => 0,
            'softwares' => 0,
            'sources' => 0,
            'styles' => 0,
            //'subsections' => 0
        );

        foreach($this->_additionals as $addName => $adds)
        {
            if($disables[$addName] || empty($adds)) continue;

            $additionalDataTable = 'template' . $addName;
            $additionalData_TemplatesTable = 'template' . $addName . '_templates';

            switch($addName)
            {
                case 'categories':
                    $singleAddName = 'category';
                    break;
                case 'keywords':
                    $singleAddName = 'keyword';
                    break;
                case 'softwares':
                    $singleAddName = 'software';
                    $additionalData_TemplatesTable = 'templates_templatesoftwares';
                    break;
                case 'sources':
                    $singleAddName = 'source';
                    $additionalData_TemplatesTable = 'templates_templatesources';
                    break;
                case 'styles':
                    $singleAddName = 'style';
                    $additionalData_TemplatesTable = 'templates_templatestyles';
                    break;
            }

            foreach($adds as $addId)
            {
                if ($this->_idExists($addId, $additionalDataTable))
                {
                    $data = array();
                    $data['template_id'] = $this->_id;
                    $data['template' . $singleAddName . '_id'] = $addId;

                    if (VERBOSE)
                    {
                        echo "<p>Inserting $singleAddName # $addId for template # {$this->_id}</p>";
                    }


                    $this->_db->insert($this->_tableNames[$additionalData_TemplatesTable], $data);

                }
                else
                {
                    throw new Kohana_Database_Exception('Such ' . $singleAddName . ' id ' . $addId . ' of template - ' . $this->_id . ' doesn\'t exist.');
                }

            }
        }
    }

    protected function _saveSubsections()
    {
        foreach($this->_subsections as $subsId)
        {
            if ($this->_idExists($subsId, 'subsections'))
            {
                if (VERBOSE)
                {
                    echo "<p>Inserting $subsId subsection ID for template # {$this->_id}</p>";
                }
                $this->_db->insert($this->_tableNames['templatesubsections'], array(
                    'template_id' => $this->_id,
                    'subsection_id' => $subsId,
                    'position' => '1',
                ));

                $templateSubsectionId = mysql_insert_id();

                // screenshots_templatesubsections

                if (VERBOSE)
                {
                    echo "<p>Inserting $templateSubsectionId template subsection ID for screenshot # {$this->_largePreview->getId()}</p>";
                }
                $this->_db->insert($this->_tableNames['screenshots_templatesubsections'], array(
                    'screenshot_id' => $this->_largePreview->getId(),
                    'templatesubsection_id' => $templateSubsectionId
                ));
            }
            else
            {
                throw new Kohana_Database_Exception('Such subsection id ' . $subsId . ' of template - ' . $this->_id . ' doesn\'t exist.');
            }
        }
    }

    protected function _idExists($val, $tableName)
    {
        $db = $this->_db;
        $query = $db->select ('*')
            ->from ($this->_tableNames[$tableName])
            ->where (array('id' => $val))
            ->limit(1)
            ->compile ();
        $item = $db->query ($query)->as_array();
        if (isset($item[0]))
            $item = $item[0];
        return !!($item);
    }
}

class Rutemplates_Templates extends Rutemplates_DatabaseLayer
{
    protected $_templates = array();
    protected $_resultQuery = '';
    protected $_authors = array();
    protected $_templateTypeId = 68;

    protected $_countFields = array(
        'authors' => array(),
        'categories' => array(),
        'packages' => array(254)
    );
    protected $_adminMailSettings = array(
        'to' => 'terion.moto@gmail.com',
        'subject' => 'Rutemplates inserting templates Exception'
    );

    protected $_dataTemplateType = array(
        'id' => '68',
        'name' => 'Premium Moto Cms Html Template Ru',
        'url_name' => 'moto-cms-html-templates-ru',
        'is_edited' => '1',
        'visibility' => '1',
        'html_name' => 'Русские MotoCMS HTML шаблоны',
        'list_name' => 'Русские MotoCMS HTML шаблоны',
        'position' => '0',
        'position_featured' => '0',
        'template_count' => '10',
        'layout' => '',
        'enabled_template_count' => '10'
    );


    function __construct($templateTypeId)
    {
        $this->_db = Database::instance();
        $this->_templateTypeId = $templateTypeId;
        $this->_dataTemplateType['id'] = $templateTypeId;
    }

    function addTemplates($templates = array())
    {
        foreach($templates as $templateData)
        {
            $templateData['templatetype_id'] = $this->_templateTypeId;
            $t = new Rutemplates_Template($templateData['main']);
            $t->additionals($templateData['additional']);
            $t->addSmallPreview(new Rutemplates_SmallPreview($templateData['smallPreview']));
            $t->addLargePreview(new Rutemplates_LargePreview($templateData['largePreview']));

            $this->addTemplate($t);
        }
    }

    function addTemplate(Rutemplates_Template $template)
    {
        $this->_templates[$template->getId()] = $template;

        if (!in_array($template->getAuthorId(), $this->_countFields['authors']))
        {
            $this->_countFields['authors'][] = $template->getAuthorId();
        }

        $this->_countFields['categories'] = array_unique(array_merge($this->_countFields['categories'], $template->getCategoryIds()));
    }

    function run()
    {
        try
        {
            // insert template type
            if (VERBOSE)
            {
                echo "<p>Inserting {$this->_dataTemplateType['id']} template type</p>";
            }
			try 
			{
				$this->_db->insert($this->_tableNames['templatetypes'], $this->_dataTemplateType);
			}
			catch(Exception $e)
			{
				if ( strpos($e->getMessage(), 'SQL error: Duplicate entry') === false)
					throw $e;
			}
			

            foreach($this->_templates as $templateId => $template)
            {
                if (VERBOSE)
                {
                    echo "<h2> ------------------------------------ Start inserting $templateId template  ------------------------------------ </h2>";
                }
				try 
				{
					$template->save();
				}
				catch(Exception $e)
				{
					if ( strpos($e->getMessage(), 'SQL error: Duplicate entry') === false)
						throw $e;
				}
            }

            $this->_updateCountTemplates();
        }
        catch(Exception $e)
        {
            if (VERBOSE)
            {
                echo $e->getMessage();
            }
			if (strpos($_SERVER['HTTP_HOST'], '.fmt') === false)
				@mail($this->_adminMailSettings['to'], $this->_adminMailSettings['subject'], $e->getMessage());
        }
    }

    function recover($ids = array())
    {
        try
        {
            foreach($ids as $templateId)
            {
                if (VERBOSE)
                {
                    echo "<p>Deleting $templateId template</p>";
                }

                $this->_db->delete($this->_tableNames['templates'], array('id' => $templateId));
                $this->_db->delete($this->_tableNames['screenshots'], array('template_id' => $templateId));

                $this->_deleteSubsections($templateId);


                //$this->_db->delete($this->_tableNames['templatedescriptions'], array('id' => $templateId));
                $this->_db->delete($this->_tableNames['templatecategories_templates'], array('template_id' => $templateId));
                $this->_db->delete($this->_tableNames['templatekeywords_templates'], array('template_id' => $templateId));
                $this->_db->delete($this->_tableNames['templates_templatesoftwares'], array('template_id' => $templateId));
                $this->_db->delete($this->_tableNames['templates_templatesources'], array('template_id' => $templateId));
                $this->_db->delete($this->_tableNames['templates_templatestyles'], array('template_id' => $templateId));


            }

            if (VERBOSE)
            {
                echo "<p>Deleting {$this->_dataTemplateType['id']} template type</p>";
            }

            $this->_updateCountTemplates();

            $this->_db->delete($this->_tableNames['templatetypes'], array('id' => $this->_templateTypeId));
        }
        catch(Exception $e)
        {
            if (VERBOSE)
            {
                echo $e->getMessage();
            }
			if (strpos($_SERVER['HTTP_HOST'], '.fmt') === false)
				@mail($this->_adminMailSettings['to'], $this->_adminMailSettings['subject'], $e->getMessage());
        }
    }

    function needUpdate($templateTypeId, $templates = array())
    {
        $needUpdate = true;
        $db = $this->_db;

        // check template type
        $query = $db->select ('*')
            ->from ($this->_tableNames['templatetypes'])
            ->where (array('id' => $templateTypeId))
            ->limit(1)
            ->compile ();
        $item = $db->query ($query)->as_array();
        if (isset($item[0]))
        {
            $item = $item[0];
            $needUpdate = !($item);
        }

        if ($needUpdate)
        {
            return $needUpdate;
        }

        // check templates
        $query = $db->select ('*')  
            ->from ($this->_tableNames['templates'])
            ->in ('id', $templates)
            ->compile ();

        $items = $db->query ($query)->as_array();
		
		$needUpdate = (sizeof($items) !== sizeof($templates));

        return $needUpdate;
    }

    protected function _deleteSubsections($templateId)
    {
        $db = $this->_db;

        $query = $db->select ('*')
            ->from ($this->_tableNames['templatesubsections'])
            ->where (array('template_id' => $templateId))
            ->compile ();

        $items = $db->query ($query)->as_array();
        if (!empty($items))
        {
            foreach($items as $item)
            {
                if (VERBOSE)
                {
                    echo "<p>Deleting subsection {$item->id} from screenshots_templatesubsections table</p>";
                }
                $this->_db->delete($this->_tableNames['screenshots_templatesubsections'], array('templatesubsection_id' => $item->id));
            }


        }
        else
        {
            printf('There\'s no subsections to delete.');
        }

        if (VERBOSE)
        {
            echo "<p>Deleting subsections from templatesubsections table</p>";
        }
        $this->_db->delete($this->_tableNames['templatesubsections'], array('template_id' => $templateId));

    }

    protected function _updateCountTemplates()
    {
        foreach($this->_countFields as $field => $ids)
        {
            if (!empty($ids))
            {

                foreach($ids as $id)
                {
                    $where = array('id' => $id);
                    $data = array();
                    switch($field)
                    {
                        case 'authors':
                            $countTemplates = $this->_db->count_records($this->_tableNames['templates'], array('templateauthor_id' => $id));
                            $tableName = 'templateauthors';
                            break;
                        case 'categories':
                            $countTemplates = $this->_db->count_records($this->_tableNames['templatecategories_templates'], array('templatecategory_id' => $id));
                            $tableName = 'templatecategories';
                            $data['enabled_template_count'] = $countTemplates;
                            break;
                        case 'packages':
                            $countTemplates = $this->_db->count_records($this->_tableNames['templates'], array('templatepackage_id' => $id));
                            $tableName = 'templatepackages';
                            break;

                    }
                    $data['template_count'] = $countTemplates;
                    if ($countTemplates > 0)
                    {
                        if (VERBOSE)
                        {
                            echo "<p>Updating $field ID $id  count = $countTemplates </p>";
                        }
                        $this->_db->update($this->_tableNames[$tableName], array('template_count' => $countTemplates), $where);

                    }
                }
            }
        }

    }
}