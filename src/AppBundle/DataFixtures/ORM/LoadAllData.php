<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\BlockTemplate;
use AppBundle\Entity\CvData;
use AppBundle\Entity\TemplatType;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Template;
use AppBundle\Entity\TemplateSlot;
use AppBundle\Entity\User;
use AppBundle\Entity\CV;
use AppBundle\Entity\BlockData;
use AppBundle\Entity\Theme;

class LoadAllData implements FixtureInterface
{

    private $defaultFields = array(
        TemplatType::TYPE_TEXT => array(
            'title' => '',
            'text' => ''
        ),
        TemplatType::TYPE_EXPERIENCE => array(
                'title' => '',
                'blocks' => array()
        ),
        TemplatType::TYPE_EDUCATION => array(
                'title' => '',
                'blocks' => array()
        ),
        TemplatType::TYPE_CERTIFICATES => array(
                'title' => '',
                'blocks' => array()
        ),
        TemplatType::TYPE_EXPERIENCE_INNER => array(
            'date_from' => '',
            'date_to' => '',
            'position' => '',
            'company' => '',
            'description' => ''
        ),
        TemplatType::TYPE_EDUCATION_INNER => array(
            'date_from' => '',
            'date_to' => '',
            'position' => '',
            'company' => '',
            'description' => ''
        ),
        TemplatType::TYPE_SKILLS => array(
                'title' => '',
                'blocks' => array()
        ),
        TemplatType::TYPE_SKILLS_INNER => array(
            'title' => '',
            'skill' => '0'
        )

    );

    public function getDefaultFields(int $type) {
        return $this->defaultFields[$type];
    }

    public function load(ObjectManager $manager)
    {
        $template1 = new Template();
        $template1->setTitle('Default');
        $template1->setTemplatePath('default');
        $manager->persist($template1);

        $templateSlot1_1 = new TemplateSlot();
        $templateSlot1_1->setTemplate($template1);
        $templateSlot1_1->setTitle('Top header');
        $templateSlot1_1->setWildcard('top_header');
        $manager->persist($templateSlot1_1);

        $templateSlot1_2 = new TemplateSlot();
        $templateSlot1_2->setTemplate($template1);
        $templateSlot1_2->setTitle('Main left slot');
        $templateSlot1_2->setWildcard('main_left_info');
        $manager->persist($templateSlot1_2);

        $templateSlot1_3 = new TemplateSlot();
        $templateSlot1_3->setTemplate($template1);
        $templateSlot1_3->setTitle('Main right slot');
        $templateSlot1_3->setWildcard('main_right_info');
        $manager->persist($templateSlot1_3);

        $block1_1 = new BlockTemplate();
        $block1_1->setTitle('Personal info headline');
        $block1_1->setType(BlockTemplate::TYPE_FIXED);
        $block1_1->setSlot($templateSlot1_1);
        $block1_1->setTemplate($template1);
        $block1_1->setHtmlSource('<div class="container"><div class="row"><div class="col-xs-12" data-zone="static"><div id="top"><div class="item" data-block-id="{{ block_data.id }}" data-block-type="'.BlockTemplate::TYPE_FIXED.'" data-is-draggable="false" data-is-editable="true" data-is-deletable="false"><div class="big-title pull-left"><h1 class="title"><span data-key="full_name" data-placeholder="John">{{ full_name }}</span></h1><h3 class="subtitle" data-key="title" data-placeholder="Your title">{{ title }}</h3></div><div class="contacts pull-right"><a href="mailto:{{ email }}" data-key="email" data-placeholder="john@example.com">{{ email }}</a> <a href="tel:{{ phone }}" data-key="phone" data-placeholder="Your phone">{{ phone }}</a></div><div class="clear"></div></div></div></div></div></div>');
        $block1_1->setAvailableFields(json_encode(array(
                'full_name' => '',
                'title' => '',
                'email' => '',
                'phone' => ''
            )));
        $manager->persist($block1_1);

        $block1_2 = new BlockTemplate();
        $block1_2->setTitle('Summary');
        $block1_2->setType(BlockTemplate::TYPE_TEXT);
        $block1_2->setSlot($templateSlot1_2);
        $block1_2->setTemplate($template1);
        $block1_2->setHtmlSource('<div class="item" data-block-id="{{ block_data.id }}" data-block-type="'.BlockTemplate::TYPE_TEXT.'" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"><h2 class="title"><i class="glyphicon glyphicon-user"></i><span data-key="title" data-placeholder="Title">{{ title }}</span></h2><p data-key="text" data-placeholder="Text goes here">{{ text }}</p>
            </div>');
        $block1_2->setAvailableFields(
            json_encode(array(
                'title' => '',
                'text' => ''
            )));
        $manager->persist($block1_2);

        $block1_3 = new BlockTemplate();
        $block1_3->setTitle('Experience');
        $block1_3->setType(BlockTemplate::TYPE_EXPERIENCE);
        $block1_3->setSlot($templateSlot1_2);
        $block1_3->setTemplate($template1);
        $block1_3->setHtmlSource('<div class="item" data-block-id="{{ block_data.id }}" data-block-type="'.BlockTemplate::TYPE_EXPERIENCE.'" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"><h2 class="title"><i class="glyphicon glyphicon-briefcase"></i><span data-key="title">Experience</span></h2><ul class="timeline" data-child-block-type="'.BlockTemplate::TYPE_EXPERIENCE_INNER.'" data-key="blocks">{{ blocks|raw }}</ul></div>');
        $block1_3->setAvailableFields(
            json_encode(array(
                'title' => '',
                'blocks' => array()
            )));
        $manager->persist($block1_3);

        $block1_3_1 = new BlockTemplate();
        $block1_3_1->setParent($block1_3);
        $block1_3_1->setTitle('Experience entry');
        $block1_3_1->setType(BlockTemplate::TYPE_EXPERIENCE_INNER);
        $block1_3_1->setTemplate($template1);
        $block1_3_1->setHtmlSource('<li><div class="icon"></div><div class="content"><div class="date"><span data-key="date_from" data-placeholder="Date from">{{ date_from }}</span> - <span data-key="date_to" data-placeholder="Date to">{{ date_to }}</span></div><h3 class="position" data-key="position" data-placeholder="Position">{{ position }}</h3><h3 class="subtitle" data-key="company" data-placeholder="Company" data-is-child="true">{{ company }}</h3><h4 data-placeholder="Description" data-key="description">{{ description }}</h4></div></li>');
        $block1_3_1->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EXPERIENCE_INNER))
        );
        $manager->persist($block1_3_1);

        $block1_4 = new BlockTemplate();
        $block1_4->setTitle('Education');
        $block1_4->setType(BlockTemplate::TYPE_EDUCATION);
        $block1_4->setSlot($templateSlot1_2);
        $block1_4->setTemplate($template1);
        $block1_4->setHtmlSource('<div class="item" data-block-id="{{ block_data.id }}" data-block-type="'.BlockTemplate::TYPE_EDUCATION.'" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"><h2 class="title"><i class="glyphicon glyphicon-education"></i><span data-key="title">Education</span></h2><ul class="timeline" data-child-block-type="'.BlockTemplate::TYPE_EDUCATION_INNER.'" data-key="blocks">{{ blocks|raw }}</ul></div>');
        $block1_4->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EDUCATION)))
        ;
        $manager->persist($block1_4);

        $block1_4_1 = new BlockTemplate();
        $block1_4_1->setParent($block1_4);
        $block1_4_1->setTitle('Education entry');
        $block1_4_1->setType(BlockTemplate::TYPE_EDUCATION_INNER);
        $block1_4_1->setTemplate($template1);
        $block1_4_1->setHtmlSource('<li><div class="icon"></div><div class="content"><div class="date"><span data-key="date_from">{{ date_from }}</span> - <span data-key="date_to">{{ date_to }}</span></div><h3 class="position" data-key="position">{{ position }}</h3><h3 class="subtitle" data-key="company">{{ company }}</h3><h4 data-key="description">{{ description }}</h4></div></li>');
        $block1_4_1->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EDUCATION_INNER))
        );
        $manager->persist($block1_4_1);

        $block1_5 = new BlockTemplate();
        $block1_5->setTitle('Skills');
        $block1_5->setType(BlockTemplate::TYPE_SKILLS);
        $block1_5->setSlot($templateSlot1_3);
        $block1_5->setTemplate($template1);
        $block1_5->setHtmlSource('<div class="item" data-block-id="{{ block_data.id }}" data-block-type="'.BlockTemplate::TYPE_SKILLS.'" data-is-draggable="true" data-is-editable="true" data-is-deletable="true">
              <h2 class="title"><i class="glyphicon glyphicon-tasks"></i><span data-key="title">Skills</span></h2><div data-child-block-type="'.BlockTemplate::TYPE_SKILLS_INNER.'" data-key="blocks">{{ blocks|raw }}</div></div>');
        $block1_5->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_SKILLS))
        );
        $manager->persist($block1_5);

        $block1_5_1 = new BlockTemplate();
        $block1_5_1->setParent($block1_5);
        $block1_5_1->setTitle('Skills/languages entry');
        $block1_5_1->setType(BlockTemplate::TYPE_SKILLS_INNER);
        $block1_5_1->setTemplate($template1);
        $block1_5_1->setHtmlSource('<div class="skills-group" data-key="skill" data-value="{{ skill }}"><label data-key="title">{{ title }}</label><ul class="skills">{% if skill > 0 %}{% for i in 1..skill %}<li class="completed"></li>{% endfor %}{% endif %}{% if skill < 10 %}{% for i in 1..(10-skill) %}<li></li>{% endfor %}{% endif %}</ul></div>');
        $block1_5_1->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_SKILLS_INNER))
        );
        $manager->persist($block1_5_1);

        $this->addTemplates($manager);

        $user1 = new User();
        $user1->setUsername('TestauskasUsername');
        $user1->setEmail('testinis.meska@example.com');
        $user1->setPlainPassword('test');
        $user1->setEnabled(true);        
        $manager->persist($user1);


        $user_2 = new User();
        $user_2->setUsername("oleg@sviderskij.lt");
        $user_2->setEmail("oleg@sviderskij.lt");
        $user_2->setPlainPassword("test");
        $user_2->setEnabled(true);
        $manager->persist($user_2);

        $theme1_1 = new Theme();
        $theme1_1->setTemplate($template1);
        $theme1_1->setBackgroundColor('455969');
        $theme1_1->setPageColor('ffffff');
        $theme1_1->setTitleColor('000000');
        $theme1_1->setParagraphColor('777777');
        $theme1_1->setPrimaryColor('288dd5');
        $theme1_1->setCssSource('styles.css');
        $manager->persist($theme1_1);

        $theme1_2 = new Theme();
        $theme1_2->setTemplate($template1);
        $theme1_2->setBackgroundColor('f1f5f8');
        $theme1_2->setPageColor('ffffff');
        $theme1_2->setTitleColor('000000');
        $theme1_2->setParagraphColor('777777');
        $theme1_2->setPrimaryColor('288dd5');
        $theme1_2->setCssSource('1_2.css');
        $manager->persist($theme1_2);

        $theme1_3 = new Theme();
        $theme1_3->setTemplate($template1);
        $theme1_3->setBackgroundColor('333111');
        $theme1_3->setPageColor('555000');
        $theme1_3->setTitleColor('555000');
        $theme1_3->setParagraphColor('555000');
        $theme1_3->setPrimaryColor('555000');
        $theme1_3->setCssSource('byrka.css');
        $manager->persist($theme1_3);

        $theme1_4 = new Theme();
        $theme1_4->setTemplate($template1);
        $theme1_4->setBackgroundColor('333111');
        $theme1_4->setPageColor('555000');
        $theme1_4->setTitleColor('555000');
        $theme1_4->setParagraphColor('555000');
        $theme1_4->setPrimaryColor('555000');
        $theme1_4->setCssSource('lopas.css');
        $manager->persist($theme1_4);

        $theme1_5 = new Theme();
        $theme1_5->setTemplate($template1);
        $theme1_5->setBackgroundColor('333111');
        $theme1_5->setPageColor('555000');
        $theme1_5->setTitleColor('555000');
        $theme1_5->setParagraphColor('555000');
        $theme1_5->setPrimaryColor('555000');
        $theme1_5->setCssSource('qwerty.css');
        $manager->persist($theme1_5);        

        $cv1 = new CV();
        $cv1->setUser($user1);
        $cv1->setUrl('my_great_cv');
        $cv1->setTemplate($template1);
        $cv1->setTheme($theme1_1);
        $manager->persist($cv1);


        $cvData_block_template = new CvData();
        $cvData_block_template->setCv($cv1);
        $cvData_block_template->setData(array(
            'blocks' => ''
        ));

        $cvData1_6 = new CvData();
        $cvData1_6->setCv($cv1);
        $cvData1_6->setData(array('full_name' => 'Erikas Mališauskas'));
        $cvData1_6->setField("full_name");
        $cvData1_6->setType(BlockTemplate::TYPE_FIXED);
        $manager->persist($cvData1_6);

        $cvData1_3 = new CvData();
        $cvData1_3->setCv($cv1);
        $cvData1_3->setData(array('title' => 'UI/UX designer'));
        $cvData1_3->setType(BlockTemplate::TYPE_FIXED);
        $cvData1_3->setField("title");
        $manager->persist($cvData1_3);

        $cvData1_4 = new CvData();
        $cvData1_4->setCv($cv1);
        $cvData1_4->setData(array('email' => 'erikas@malisauskas.lt'));
        $cvData1_4->setType(BlockTemplate::TYPE_FIXED);
        $cvData1_4->setField("email");
        $manager->persist($cvData1_4);

        $cvData1_5 = new CvData();
        $cvData1_5->setCv($cv1);
        $cvData1_5->setData(array('phone' => '+370 (629) 26 815'));
        $cvData1_5->setType(BlockTemplate::TYPE_FIXED);
        $cvData1_5->setField("phone");
        $manager->persist($cvData1_5);

        $blockData1_1 = new BlockData();
        $blockData1_1->setCV($cv1);
        $blockData1_1->setTemplateSlot($templateSlot1_1);
        $blockData1_1->setBlockTemplate($block1_1);
        $blockData1_1->addCvData($cvData1_3);
        $blockData1_1->addCvData($cvData1_4);
        $blockData1_1->addCvData($cvData1_5);
        $blockData1_1->addCvData($cvData1_6);
        $manager->persist($blockData1_1);


        $cvData_summary = new CvData();
        $cvData_summary->setCv($cv1);
        $cvData_summary->setData(array('title' => 'Summary', 'text' => 'Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Vestibulum id ligula porta felis euismod semper. Nullam quis risus eget urna mollis ornare vel eu leo. Lorem ipsum dolor sit amet, consectetur adipiscing elit.'));
        $cvData_summary->setType(BlockTemplate::TYPE_TEXT);
        $manager->persist($cvData_summary);


        $blockData1_2 = new BlockData();
        $blockData1_2->setCV($cv1);
        $blockData1_2->setTemplateSlot($templateSlot1_2);
        $blockData1_2->setBlockTemplate($block1_2);
        $blockData1_2->addCvData($cvData_summary);
        $manager->persist($blockData1_2);

        $education_data_template = array(
            "title" => "Education",
            "blocks" => array(
                array(
                    'date_from' => '2014 June',
                    'date_to' => 'present',
                    'position' => 'Senior UI/UX designer 1',
                    'company' => 'MailerLite',
                    'description' => 'Padsobniku dirbau'
                ),
                array(
                    'date_from' => '2014 June',
                    'date_to' => 'present',
                    'position' => 'Senior UI/UX designer 1',
                    'company' => 'MailerLite',
                    'description' => 'Padsobniku dirbau'
                ),
                array(
                    'date_from' => '2014 June',
                    'date_to' => 'present',
                    'position' => 'Senior UI/UX designer 1',
                    'company' => 'MailerLite',
                    'description' => 'Padsobniku dirbau'
                )
            )

        );

        $cvData_block_1 = clone ($cvData_block_template);
        $cvData_block_1->setData($education_data_template);
        $cvData_block_1->setType(BlockTemplate::TYPE_EDUCATION);
        $manager->persist($cvData_block_1);

        $blockData1_3 = new BlockData();
        $blockData1_3->setCV($cv1);
        $blockData1_3->addCvData($cvData_block_1);
        $blockData1_3->setTemplateSlot($templateSlot1_2);
        $blockData1_3->setBlockTemplate($block1_3);
        $manager->persist($blockData1_3);

        $cvData_template = new CvData();
        $cvData_template->setCv($cv1);
        $cvData_template->setData(array(
            'date_from' => '2014 June',
            'date_to' => 'present',
            'position' => 'Senior UI/UX designer 1',
            'company' => 'MailerLite',
            'description' => 'Padsobniku dirbau'
        ));


        $cvData_block_2 = clone ($cvData_block_template);
        $cvData_block_2->setType(BlockTemplate::TYPE_EDUCATION);
        $cvData_block_2->setData($education_data_template);
        $manager->persist($cvData_block_2);



        $blockData1_4 = new BlockData();
        $blockData1_4->setCV($cv1);
        $blockData1_4->addCvData($cvData_block_2);
        $blockData1_4->setTemplateSlot($templateSlot1_2);
        $blockData1_4->setBlockTemplate($block1_4);
        $manager->persist($blockData1_4);

        $cvData_block_3 = clone ($cvData_block_template);
        $cvData_block_3->setType(BlockTemplate::TYPE_SKILLS);
        $cvData_block_3->setData(array(
            'title' => 'Skill',
            'blocks' => array(
                array(
                    'title' => 'HTML/CSS',
                    'skill' => '8'
                ),
                array(
                    'title' => 'HTML/CSS',
                    'skill' => '8'
                ),
                array(
                    'title' => 'HTML/CSS',
                    'skill' => '8'
                )
            )
        ));
        $manager->persist($cvData_block_3);

        $blockData1_5 = new BlockData();
        $blockData1_5->setCV($cv1);
        $blockData1_5->addCvData($cvData_block_3);
        $blockData1_5->setTemplateSlot($templateSlot1_3);
        $blockData1_5->setBlockTemplate($block1_5);
        $manager->persist($blockData1_5);

        $cvData_block_4 = clone ($cvData_block_template);
        $cvData_block_4->setData(array(
            'title' => 'Skill',
            'blocks' => array(
                array(
                    'title' => 'Lithuanian',
                    'skill' => '8'
                ),
                array(
                    'title' => 'English',
                    'skill' => '8'
                ),
                array(
                    'title' => 'Russian',
                    'skill' => '8'
                )
            )
        ));
        $cvData_block_4->setType(BlockTemplate::TYPE_SKILLS);

        $manager->persist($cvData_block_4);

        $blockData1_6 = new BlockData();
        $blockData1_6->setCV($cv1);
        $blockData1_6->addCvData($cvData_block_4);
        $blockData1_6->setTemplateSlot($templateSlot1_3);
        $blockData1_6->setBlockTemplate($block1_5);
        $manager->persist($blockData1_6);

        $manager->flush();
    }


    private function addTemplates(ObjectManager $manager) {
        $template1 = new Template();
        $template1->setTitle('Standart');
        $template1->setTemplatePath('standart');
        $manager->persist($template1);

        $templateSlot1_1 = new TemplateSlot();
        $templateSlot1_1->setTemplate($template1);
        $templateSlot1_1->setTitle('Standart left slot');
        $templateSlot1_1->setWildcard('standart_left_info');
        $manager->persist($templateSlot1_1);

        $templateSlot1_2 = new TemplateSlot();
        $templateSlot1_2->setTemplate($template1);
        $templateSlot1_2->setTitle('Standart right slot');
        $templateSlot1_2->setWildcard('standart_right_slot');
        $manager->persist($templateSlot1_2);

        $block1_1 = new BlockTemplate();
        $block1_1->setTitle('Personal info headline');
        $block1_1->setType(BlockTemplate::TYPE_FIXED);
        $block1_1->setSlot($templateSlot1_1);
        $block1_1->setTemplate($template1);
        $block1_1->setHtmlSource('<div class="person" class="item" data-block-id="{{block_data.id}}" data-block-type="0" data-is-draggable="false" data-is-editable="true" data-is-deletable="false"> <div class="photo"><img src="{{asset(\'templates/standart/img/photo.jpg\')}}" alt=""/></div><div class="name" data-key="full_name" data-placeholder="John">{{ full_name }}</div><div class="statusquo" data-key="title" data-placeholder="UI/UX designer">{{title}}</div></div>');
        $block1_1->setAvailableFields(json_encode(array(
            'full_name' => '',
            'title' => ''
        )));
        $manager->persist($block1_1);

        $block1_2 = new BlockTemplate();
        $block1_2->setTitle('Summary');
        $block1_2->setType(BlockTemplate::TYPE_TEXT);
        $block1_2->setSlot($templateSlot1_1);
        $block1_2->setTemplate($template1);
        $block1_2->setHtmlSource('<div class="group item" data-block-id="{{ block_data.id }}" data-block-type="1" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"> <div class="group title" data-key="title" data-placeholder="Title">{{ title }}</div> <div class="group content" data-key="text" data-placeholder="Text goes here"> {{ text }} </div> </div>');
        $block1_2->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_TEXT)));
        $manager->persist($block1_2);

        $block1_3 = new BlockTemplate();
        $block1_3->setTitle('Experience');
        $block1_3->setType(BlockTemplate::TYPE_EXPERIENCE);
        $block1_3->setSlot($templateSlot1_2);
        $block1_3->setTemplate($template1);
        $block1_3->setHtmlSource('<div class="group item" data-block-id="{{block_data.id}}" data-block-type="4" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"> <div class="group title" data-key="title">Working Experience</div><div class="group content"> <div class="blocks timeline" data-child-block-type="5" data-key="blocks">{{blocks|raw}}</div></div></div>');
        $block1_3->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EXPERIENCE)));
        $manager->persist($block1_3);

        $block1_3_1 = new BlockTemplate();
        $block1_3_1->setParent($block1_3);
        $block1_3_1->setTitle('Experience entry');
        $block1_3_1->setType(BlockTemplate::TYPE_EXPERIENCE_INNER);
        $block1_3_1->setTemplate($template1);
        $block1_3_1->setHtmlSource('<div class="row"> <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 date"> <span data-placeholder="Date from">{{date_from}}</span> <span>-</span> <span data-key="date_to" data-placeholder="Date to">{{date_to}}</span> </div><div class="col-lg-10 col-md-9 col-sm-9 col-xs-12"> <div class="title" data-key="position" data-placeholder="Position">{{position}}</div><div class="company" data-key="company" data-placeholder="Company" data-is-child="true">{{company}}</div><div class="description" ata-placeholder="Description" data-key="description">{{description}}</div></div></div>');
        $block1_3_1->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EXPERIENCE_INNER)));
        $manager->persist($block1_3_1);

        $block1_4 = new BlockTemplate();
        $block1_4->setTitle('Education');
        $block1_4->setType(BlockTemplate::TYPE_EDUCATION);
        $block1_4->setSlot($templateSlot1_2);
        $block1_4->setTemplate($template1);
        $block1_4->setHtmlSource('<div class="group item" data-block-id="{{block_data.id}}" data-block-type="4" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"> <div class="group title" data-key="title">Working Experience</div><div class="group content"> <div class="blocks timeline" data-child-block-type="5" data-key="blocks">{{blocks|raw}}</div></div></div>');
        $block1_4->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EDUCATION)));
        $manager->persist($block1_4);

        $block1_4_1 = new BlockTemplate();
        $block1_4_1->setParent($block1_4);
        $block1_4_1->setTitle('Education entry');
        $block1_4_1->setType(BlockTemplate::TYPE_EDUCATION_INNER);
        $block1_4_1->setTemplate($template1);
        $block1_4_1->setHtmlSource('<div class="row"> <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 date"> <span data-placeholder="Date from">{{date_from}}</span> <span>-</span> <span data-key="date_to" data-placeholder="Date to">{{date_to}}</span> </div><div class="col-lg-10 col-md-9 col-sm-9 col-xs-12"> <div class="title" data-key="position" data-placeholder="Position">{{position}}</div><div class="company" data-key="company" data-placeholder="Company" data-is-child="true">{{company}}</div><div class="description" ata-placeholder="Description" data-key="description">{{description}}</div></div></div>');
        $block1_4_1->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EDUCATION_INNER)));
        $manager->persist($block1_4_1);

        $block1_5 = new BlockTemplate();
        $block1_5->setTitle('Skills');
        $block1_5->setType(BlockTemplate::TYPE_SKILLS);
        $block1_5->setSlot($templateSlot1_1);
        $block1_5->setTemplate($template1);
        $block1_5->setHtmlSource('<div class="group item" data-block-id="{{block_data.id}}" data-block-type="2" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"> <div class="group title" data-key="title">Languages</div><div class="group content"> <div class="blocks indicator">{{blocks|raw}}</div></div></div>');
        $block1_5->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_SKILLS)));
        $manager->persist($block1_5);

        $block1_5_1 = new BlockTemplate();
        $block1_5_1->setParent($block1_5);
        $block1_5_1->setTitle('Skills/languages entry');
        $block1_5_1->setType(BlockTemplate::TYPE_SKILLS_INNER);
        $block1_5_1->setTemplate($template1);
        $block1_5_1->setHtmlSource('<div data-key="skill" data-value="{{skill}}"> <div class="title" data-key="title">{{title}}</div><div class="bar"> <div class="progress" style="width: {{ skill }}0%"></div></div></div>');
        $block1_5_1->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EDUCATION_INNER)));
        $manager->persist($block1_5_1);
    }
}