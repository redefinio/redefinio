<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\BlockTemplate;
use AppBundle\Entity\CvData;
use AppBundle\Entity\TemplatType;
use AppBundle\Entity\UserThemes;
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

        $templateSlot1_4 = new TemplateSlot();
        $templateSlot1_4->setTemplate($template1);
        $templateSlot1_4->setTitle('Top contacts');
        $templateSlot1_4->setWildcard('top_contacts');
        $manager->persist($templateSlot1_4);

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
        $block1_1->setHtmlSource('<div class="item" data-block-id="{{block_data.id}}" data-block-type="'.BlockTemplate::TYPE_FIXED.'" data-is-draggable="false" data-is-editable="true" data-is-deletable="false"> <h1 class="title"><span data-key="full_name" data-placeholder="John" data-required="true">{{full_name}}</span> </h1> <h3 class="subtitle" data-key="title" data-placeholder="Your title" data-required="true">{{title}}</h3></div>');
        $block1_1->setAvailableFields(json_encode(array(
                'full_name' => '',
                'title' => ''
            )));
        $manager->persist($block1_1);

        $block_contact = new BlockTemplate();
        $block_contact->setTitle('Personal constacts headline');
        $block_contact->setType(BlockTemplate::TYPE_FIXED);
        $block_contact->setSlot($templateSlot1_4);
        $block_contact ->setTemplate($template1);
        $block_contact->setHtmlSource('<div class="item" data-block-id="{{block_data.id}}" data-block-type="'.BlockTemplate::TYPE_FIXED.'" data-is-draggable="false" data-is-editable="true" data-is-deletable="false"> <a href="mailto:{{email}}" data-key="email" data-placeholder="john@example.com">{{email}}</a><a href="tel:{{phone}}" data-key="phone" data-placeholder="Your phone">{{phone}}</a><a href="#" data-key="location" data-placeholder="Your location">{{location}}</a></div>');
        $block_contact->setAvailableFields(json_encode(array(
            'email' => '',
            'phone' => '',
            'location' => '',
            'photo' => '',
            'contact_title' => 'Contacts'
        )));
        $manager->persist($block_contact);

        $block1_2 = new BlockTemplate();
        $block1_2->setTitle('Summary');
        $block1_2->setType(BlockTemplate::TYPE_TEXT);
        $block1_2->setSlot($templateSlot1_2);
        $block1_2->setTemplate($template1);
        $block1_2->setHtmlSource('<div class="item" data-block-id="{{ block_data.id }}" data-block-type="'.BlockTemplate::TYPE_TEXT.'" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"><h2 class="title"><i class="glyphicon glyphicon-user"></i><span data-key="title" data-placeholder="Summary">{{ title }}</span></h2><p data-key="text" data-placeholder="Text goes here">{{ text|raw }}</p></div>');
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
        $block1_3->setHtmlSource('<div class="item" data-block-id="{{ block_data.id }}" data-block-type="'.BlockTemplate::TYPE_EXPERIENCE.'" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"><h2 class="title"><i class="glyphicon glyphicon-briefcase"></i><span data-key="title" data-placeholder="Work experience">{{title}}</span></h2><ul class="timeline" data-child-block-type="'.BlockTemplate::TYPE_EXPERIENCE_INNER.'" data-key="blocks">{{ blocks|raw }}</ul></div>');
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
        $block1_3_1->setHtmlSource('<li><div class="icon"></div><div class="content"><div class="date"><div data-key="date_from" data-placeholder="Date from">{{ date_from }}</div> - <div data-key="date_to" data-placeholder="Date to">{{ date_to }}</div></div><h3 class="position" data-key="position" data-placeholder="Position">{{ position }}</h3><h3 class="subtitle" data-key="company" data-placeholder="Company" data-is-child="true">{{ company }}</h3><h4 data-placeholder="Description - use this area to describe your daily tasks, responsibilities, etc." data-key="description">{{ description }}</h4></div></li>');
        $block1_3_1->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EXPERIENCE_INNER))
        );
        $manager->persist($block1_3_1);

        $block1_4 = new BlockTemplate();
        $block1_4->setTitle('Education');
        $block1_4->setType(BlockTemplate::TYPE_EDUCATION);
        $block1_4->setSlot($templateSlot1_2);
        $block1_4->setTemplate($template1);
        $block1_4->setHtmlSource('<div class="item" data-block-id="{{ block_data.id }}" data-block-type="'.BlockTemplate::TYPE_EDUCATION.'" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"><h2 class="title"><i class="glyphicon glyphicon-education"></i><span data-placeholder="Education" data-key="title">{{title}}</span></h2><ul class="timeline" data-child-block-type="'.BlockTemplate::TYPE_EDUCATION_INNER.'" data-key="blocks">{{ blocks|raw }}</ul></div>');
        $block1_4->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EDUCATION)))
        ;
        $manager->persist($block1_4);

        $block1_4_1 = new BlockTemplate();
        $block1_4_1->setParent($block1_4);
        $block1_4_1->setTitle('Education entry');
        $block1_4_1->setType(BlockTemplate::TYPE_EDUCATION_INNER);
        $block1_4_1->setTemplate($template1);
        $block1_4_1->setHtmlSource('<li><div class="icon"></div><div class="content"><div class="date"><span data-placeholder="Date from" data-key="date_from">{{ date_from }}</span> - <span data-placeholder="Date to" data-key="date_to">{{ date_to }}</span></div><h3 class="position" data-placeholder="Course name" data-key="position">{{ position }}</h3><h3 class="subtitle" data-placeholder="University/College name" data-key="company">{{ company }}</h3><h4 data-placeholder="Description" data-key="description">{{ description }}</h4></div></li>');
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
        $block1_5_1->setHtmlSource('<div class="skills-group" data-key="skill" data-value="{{ skill }}"><label data-placeholder="Skill name" data-key="title">{{ title }}</label><ul class="skills">{% if skill > 0 %}{% for i in 1..skill %}<li class="completed"></li>{% endfor %}{% endif %}{% if skill < 10 %}{% for i in 1..(10-skill) %}<li></li>{% endfor %}{% endif %}</ul></div>');
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
        $theme1_1->setBackgroundColor('f1f5f8');
        $theme1_1->setPageColor('ffffff');
        $theme1_1->setTitleColor('1dce79');
        $theme1_1->setParagraphColor('2b2724');
        $theme1_1->setPrimaryColor('8b8b8b');
        $theme1_1->setCssSource('styles.css');
        $manager->persist($theme1_1);

        $theme1_2 = new Theme();
        $theme1_2->setTemplate($template1);
        $theme1_2->setBackgroundColor('f1f5f8');
        $theme1_2->setPageColor('ffffff');
        $theme1_2->setTitleColor('3db0e5');
        $theme1_2->setParagraphColor('2A2720');
        $theme1_2->setPrimaryColor('8b8b8b');
        $theme1_2->setCssSource('1_2.css');
        $manager->persist($theme1_2);

        $theme1_3 = new Theme();
        $theme1_3->setTemplate($template1);
        $theme1_3->setBackgroundColor('f1f5f8');
        $theme1_3->setPageColor('555000');
        $theme1_3->setTitleColor('065ecd');
        $theme1_3->setParagraphColor('2b2724');
        $theme1_3->setPrimaryColor('8b8b8b');
        $theme1_3->setCssSource('1_7.css');
        $manager->persist($theme1_3);

        $theme1_4 = new Theme();
        $theme1_4->setTemplate($template1);
        $theme1_4->setBackgroundColor('f1f5f8');
        $theme1_4->setPageColor('555000');
        $theme1_4->setTitleColor('000');
        $theme1_4->setParagraphColor('2b2724');
        $theme1_4->setPrimaryColor('8b8b8b');
        $theme1_4->setCssSource('1_8.css');
        $manager->persist($theme1_4);

        $theme1_5 = new Theme();
        $theme1_5->setTemplate($template1);
        $theme1_5->setBackgroundColor('f1f5f8');
        $theme1_5->setPageColor('555000');
        $theme1_5->setTitleColor('00c897');
        $theme1_5->setParagraphColor('2b2724');
        $theme1_5->setPrimaryColor('8b8b8b');
        $theme1_5->setCssSource('1_9.css');
        $manager->persist($theme1_5);

        $theme1_6 = new Theme();
        $theme1_6->setTemplate($template1);
        $theme1_6->setBackgroundColor('f1f5f8');
        $theme1_6->setPageColor('555000');
        $theme1_6->setTitleColor('ff9600');
        $theme1_6->setParagraphColor('2b2724');
        $theme1_6->setPrimaryColor('8b8b8b');
        $theme1_6->setCssSource('1_3.css');
        $manager->persist($theme1_6);

        $theme1_7 = new Theme();
        $theme1_7->setTemplate($template1);
        $theme1_7->setBackgroundColor('f1f5f8');
        $theme1_7->setPageColor('555000');
        $theme1_7->setTitleColor('a976de');
        $theme1_7->setParagraphColor('2b2724');
        $theme1_7->setPrimaryColor('8b8b8b');
        $theme1_7->setCssSource('1_4.css');
        $manager->persist($theme1_7);

        $theme1_8 = new Theme();
        $theme1_8->setTemplate($template1);
        $theme1_8->setBackgroundColor('f1f5f8');
        $theme1_8->setPageColor('555000');
        $theme1_8->setTitleColor('ff4242');
        $theme1_8->setParagraphColor('2b2724');
        $theme1_8->setPrimaryColor('8b8b8b');
        $theme1_8->setCssSource('1_6.css');
        $manager->persist($theme1_8);

        $template1->setTheme($theme1_1);

        $cv1 = new CV();
        $cv1->setUser($user1);
        $cv1->setUrl('my_great_cv');
        $cv1->setTemplate($template1);
        $cv1->setPublicTemplate($template1);
        $manager->persist($cv1);


        $user_themes = new UserThemes();

        $user_themes->setCv($cv1);
        $user_themes->setTheme($theme1_1);
        $user_themes->setTemplate($template1);
        $manager->persist($user_themes);


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

        $cvData1_7 = new CvData();
        $cvData1_7->setCv($cv1);
        $cvData1_7->setData(array('location' => 'Vilnius, Lithuania'));
        $cvData1_7->setType(BlockTemplate::TYPE_FIXED);
        $cvData1_7->setField("location");
        $manager->persist($cvData1_7);

        $cvData1_8 = new CvData();
        $cvData1_8->setCv($cv1);
        $cvData1_8->setData(array('photo' => 'img/photo-placeholder.png'));
        $cvData1_8->setType(BlockTemplate::TYPE_FIXED);
        $cvData1_8->setField("photo");
        $manager->persist($cvData1_8);

        $cvData1_9 = new CvData();
        $cvData1_9->setCv($cv1);
        $cvData1_9->setData(array('contact_title' => 'Contacts'));
        $cvData1_9->setType(BlockTemplate::TYPE_FIXED);
        $cvData1_9->setField("contact_title");
        $manager->persist($cvData1_9);

        $blockData1_1 = new BlockData();
        $blockData1_1->setCV($cv1);
        $blockData1_1->setTemplateSlot($templateSlot1_1);
        $blockData1_1->setBlockTemplate($block1_1);
        $blockData1_1->addCvData($cvData1_3);
        $blockData1_1->addCvData($cvData1_4);
        $blockData1_1->addCvData($cvData1_5);
        $blockData1_1->addCvData($cvData1_6);
        $blockData1_1->addCvData($cvData1_7);
        $blockData1_1->addCvData($cvData1_8);
        $blockData1_1->addCvData($cvData1_9);
        $manager->persist($blockData1_1);

        $blockData_contacts = new BlockData();
        $blockData_contacts->setCv($cv1);
        $blockData_contacts->setTemplateSlot($templateSlot1_4);
        $blockData_contacts->setBlockTemplate($block_contact);
        $blockData_contacts->addCvData($cvData1_4);
        $blockData_contacts->addCvData($cvData1_5);
        $blockData_contacts->addCvData($cvData1_7);
        $blockData_contacts->addCvData($cvData1_8);
        $blockData_contacts->addCvData($cvData1_9);
        $manager->persist($blockData_contacts);

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

        $templateSlot1 = new TemplateSlot();
        $templateSlot1->setTemplate($template1);
        $templateSlot1->setTitle('Standart personal slot');
        $templateSlot1->setWildcard('standart_personal_info');
        $manager->persist($templateSlot1);

        $templateSlot2 = new TemplateSlot();
        $templateSlot2->setTemplate($template1);
        $templateSlot2->setTitle('Standart contacts slot');
        $templateSlot2->setWildcard('standart_contacts_slot');
        $manager->persist($templateSlot2);

        $templateSlot3 = new TemplateSlot();
        $templateSlot3->setTemplate($template1);
        $templateSlot3->setTitle('Standart right slot');
        $templateSlot3->setWildcard('standart_left_info');
        $manager->persist($templateSlot3);

        $templateSlot4 = new TemplateSlot();
        $templateSlot4->setTemplate($template1);
        $templateSlot4->setTitle('Standart right slot');
        $templateSlot4->setWildcard('standart_right_slot');
        $manager->persist($templateSlot4);

        $block1_1 = new BlockTemplate();
        $block1_1->setTitle('Personal info headline');
        $block1_1->setType(BlockTemplate::TYPE_FIXED);
        $block1_1->setSlot($templateSlot1);
        $block1_1->setTemplate($template1);
        $block1_1->setHtmlSource('<div class="person" class="item" data-block-id="{{block_data.id}}" data-block-type="0" data-is-draggable="false" data-is-editable="true" data-is-deletable="false"> <div class="photo"><img src="{{absolute_url(asset(photo))}}" alt=""/></div><div><span class="btn btn-default btn-file"><i class="fa fa-picture-o" aria-hidden="true"></i>Upload photo<input data-key="photo" type="file"></span></div><div class="name" data-key="full_name" data-placeholder="John">{{ full_name }}</div><div class="statusquo" data-key="title" data-placeholder="UI/UX designer">{{title}}</div></div>');
        $block1_1->setAvailableFields(json_encode(array(
            'full_name' => '',
            'title' => '',
            'photo' => ''
        )));
        $manager->persist($block1_1);

        $block1_2 = new BlockTemplate();
        $block1_2->setTitle('Summary');
        $block1_2->setType(BlockTemplate::TYPE_TEXT);
        $block1_2->setSlot($templateSlot3);
        $block1_2->setTemplate($template1);
        $block1_2->setHtmlSource('<div class="group item" data-block-id="{{ block_data.id }}" data-block-type="1" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"> <div class="group title" data-key="title" data-placeholder="Summary">{{ title }}</div> <div class="group content" data-key="text" data-placeholder="Text goes here"> {{ text }} </div> </div>');
        $block1_2->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_TEXT)));
        $manager->persist($block1_2);

        $block1_3 = new BlockTemplate();
        $block1_3->setTitle('Experience');
        $block1_3->setType(BlockTemplate::TYPE_EXPERIENCE);
        $block1_3->setSlot($templateSlot4);
        $block1_3->setTemplate($template1);
        $block1_3->setHtmlSource('<div class="group item" data-block-id="{{block_data.id}}" data-block-type="4" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"> <div class="group title" data-placeholder="Work experience" data-key="title">{{title}}</div><div class="group content"> <div class="blocks timeline" data-child-block-type="5" data-key="blocks">{{blocks|raw}}</div></div></div>');
        $block1_3->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EXPERIENCE)));
        $manager->persist($block1_3);

        $block1_3_1 = new BlockTemplate();
        $block1_3_1->setParent($block1_3);
        $block1_3_1->setTitle('Experience entry');
        $block1_3_1->setType(BlockTemplate::TYPE_EXPERIENCE_INNER);
        $block1_3_1->setTemplate($template1);
        $block1_3_1->setHtmlSource('<div class="row"> <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 date"> <span data-key="date_from" data-placeholder="Date from">{{date_from}}</span> <span>-</span> <span data-key="date_to" data-placeholder="Date to">{{date_to}}</span> </div><div class="col-lg-9 col-md-9 col-sm-9 col-xs-12"> <div class="title" data-key="position" data-placeholder="Position">{{position}}</div><div class="company" data-key="company" data-placeholder="Company" data-is-child="true">{{company}}</div><div class="description" data-placeholder="Description - use this area to describe your daily tasks, responsibilities, etc." data-key="description">{{description}}</div></div></div>');
        $block1_3_1->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EXPERIENCE_INNER)));
        $manager->persist($block1_3_1);

        $block1_4 = new BlockTemplate();
        $block1_4->setTitle('Education');
        $block1_4->setType(BlockTemplate::TYPE_EDUCATION);
        $block1_4->setSlot($templateSlot4);
        $block1_4->setTemplate($template1);
        $block1_4->setHtmlSource('<div class="group item" data-block-id="{{block_data.id}}" data-block-type="4" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"> <div class="group title" data-placeholder="Education" data-key="title">{{title}}</div><div class="group content"> <div class="blocks timeline" data-child-block-type="5" data-key="blocks">{{blocks|raw}}</div></div></div>');
        $block1_4->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EDUCATION)));
        $manager->persist($block1_4);

        $block1_4_1 = new BlockTemplate();
        $block1_4_1->setParent($block1_4);
        $block1_4_1->setTitle('Education entry');
        $block1_4_1->setType(BlockTemplate::TYPE_EDUCATION_INNER);
        $block1_4_1->setTemplate($template1);
        $block1_4_1->setHtmlSource('<div class="row"> <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 date"> <div data-key="date_from" data-placeholder="Date from">{{date_from}}</div> <span>-</span> <div data-key="date_to" data-placeholder="Date to">{{date_to}}</div> </div><div class="col-lg-9 col-md-9 col-sm-9 col-xs-12"> <div class="title" data-key="position" data-placeholder="Course name">{{position}}</div><div class="company" data-key="company" data-placeholder="University/College name" data-is-child="true">{{company}}</div><div class="description" data-placeholder="Description" data-key="description">{{description}}</div></div></div>');
        $block1_4_1->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_EDUCATION_INNER)));
        $manager->persist($block1_4_1);

        $block1_5 = new BlockTemplate();
        $block1_5->setTitle('Skills');
        $block1_5->setType(BlockTemplate::TYPE_SKILLS);
        $block1_5->setSlot($templateSlot3);
        $block1_5->setTemplate($template1);
        $block1_5->setHtmlSource('<div class="group item" data-block-id="{{block_data.id}}" data-block-type="2" data-is-draggable="true" data-is-editable="true" data-is-deletable="true"> <div class="group title" data-placeholder="Skills" data-key="title">{{title}}</div><div class="group content"> <div data-child-block-type="3" data-key="blocks" class="blocks indicator">{{blocks|raw}}</div></div></div>');
        $block1_5->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_SKILLS)));
        $manager->persist($block1_5);

        $block1_5_1 = new BlockTemplate();
        $block1_5_1->setParent($block1_5);
        $block1_5_1->setTitle('Skills/languages entry');
        $block1_5_1->setType(BlockTemplate::TYPE_SKILLS_INNER);
        $block1_5_1->setTemplate($template1);
        $block1_5_1->setHtmlSource('<div data-key="skill" data-value="{{skill}}" class="skills-group"> <div class="title" data-placeholder="Skill name" data-key="title">{{title}}</div><div class="skills bar"> <div class="progress" style="width: {{ skill }}0%"></div></div></div>');
        $block1_5_1->setAvailableFields(
            json_encode($this->getDefaultFields(TemplatType::TYPE_SKILLS_INNER)));
        $manager->persist($block1_5_1);


        $theme2_1 = new Theme();
        $theme2_1->setTemplate($template1);
        $theme2_1->setBackgroundColor('ff4242');
        $theme2_1->setPageColor('555000');
        $theme2_1->setTitleColor('000');
        $theme2_1->setParagraphColor('666');
        $theme2_1->setPrimaryColor('666');
        $theme2_1->setCssSource('2_2.css');
        $manager->persist($theme2_1);

        $theme2_2 = new Theme();
        $theme2_2->setTemplate($template1);
        $theme2_2->setBackgroundColor('065ecd');
        $theme2_2->setPageColor('555000');
        $theme2_2->setTitleColor('000');
        $theme2_2->setParagraphColor('666');
        $theme2_2->setPrimaryColor('666');
        $theme2_2->setCssSource('2_3.css');
        $manager->persist($theme2_2);
        
        $theme2_3 = new Theme();
        $theme2_3->setTemplate($template1);
        $theme2_3->setBackgroundColor('000');
        $theme2_3->setPageColor('555000');
        $theme2_3->setTitleColor('000');
        $theme2_3->setParagraphColor('666');
        $theme2_3->setPrimaryColor('666');
        $theme2_3->setCssSource('2_4.css');
        $manager->persist($theme2_3);
        
        $theme2_4 = new Theme();
        $theme2_4->setTemplate($template1);
        $theme2_4->setBackgroundColor('14194d');
        $theme2_4->setPageColor('555000');
        $theme2_4->setTitleColor('000');
        $theme2_4->setParagraphColor('666');
        $theme2_4->setPrimaryColor('666');
        $theme2_4->setCssSource('2_5.css');
        $manager->persist($theme2_4);
        
        $theme2_5 = new Theme();
        $theme2_5->setTemplate($template1);
        $theme2_5->setBackgroundColor('00c897');
        $theme2_5->setPageColor('555000');
        $theme2_5->setTitleColor('000');
        $theme2_5->setParagraphColor('666');
        $theme2_5->setPrimaryColor('666');
        $theme2_5->setCssSource('2_6.css');
        $manager->persist($theme2_5);
        
        $theme2_6 = new Theme();
        $theme2_6->setTemplate($template1);
        $theme2_6->setBackgroundColor('ff9600');
        $theme2_6->setPageColor('555000');
        $theme2_6->setTitleColor('000');
        $theme2_6->setParagraphColor('666');
        $theme2_6->setPrimaryColor('666');
        $theme2_6->setCssSource('2_7.css');
        $manager->persist($theme2_6);
        
        $theme2_7 = new Theme();
        $theme2_7->setTemplate($template1);
        $theme2_7->setBackgroundColor('a976de');
        $theme2_7->setPageColor('555000');
        $theme2_7->setTitleColor('000');
        $theme2_7->setParagraphColor('666');
        $theme2_7->setPrimaryColor('666');
        $theme2_7->setCssSource('2_8.css');
        $manager->persist($theme2_7);
        
        $theme2_8 = new Theme();
        $theme2_8->setTemplate($template1);
        $theme2_8->setBackgroundColor('e7cfff');
        $theme2_8->setPageColor('555000');
        $theme2_8->setTitleColor('000');
        $theme2_8->setParagraphColor('666');
        $theme2_8->setPrimaryColor('666');
        $theme2_8->setCssSource('2_9.css');
        $manager->persist($theme2_8);
        
        $theme2_9 = new Theme();
        $theme2_9->setTemplate($template1);
        $theme2_9->setBackgroundColor('eee');
        $theme2_9->setPageColor('555000');
        $theme2_9->setTitleColor('000');
        $theme2_9->setParagraphColor('666');
        $theme2_9->setPrimaryColor('666');
        $theme2_9->setCssSource('2_10.css');
        $manager->persist($theme2_9);

        $theme2_10 = new Theme();
        $theme2_10->setTemplate($template1);
        $theme2_10->setBackgroundColor('ffcfcf');
        $theme2_10->setPageColor('555000');
        $theme2_10->setTitleColor('000');
        $theme2_10->setParagraphColor('666');
        $theme2_10->setPrimaryColor('666');
        $theme2_10->setCssSource('2_11.css');
        $manager->persist($theme2_10);
        
        $theme2_11 = new Theme();
        $theme2_11->setTemplate($template1);
        $theme2_11->setBackgroundColor('cfeeff');
        $theme2_11->setPageColor('555000');
        $theme2_11->setTitleColor('000');
        $theme2_11->setParagraphColor('666');
        $theme2_11->setPrimaryColor('666');
        $theme2_11->setCssSource('2_12.css');
        $manager->persist($theme2_11);
        
        $theme2_12 = new Theme();
        $theme2_12->setTemplate($template1);
        $theme2_12->setBackgroundColor('dad2cd');
        $theme2_12->setPageColor('555000');
        $theme2_12->setTitleColor('000');
        $theme2_12->setParagraphColor('666');
        $theme2_12->setPrimaryColor('666');
        $theme2_12->setCssSource('2_13.css');
        $manager->persist($theme2_12);
        
        $theme2_13 = new Theme();
        $theme2_13->setTemplate($template1);
        $theme2_13->setBackgroundColor('00ffd5');
        $theme2_13->setPageColor('555000');
        $theme2_13->setTitleColor('000');
        $theme2_13->setParagraphColor('666');
        $theme2_13->setPrimaryColor('666');
        $theme2_13->setCssSource('2_14.css');
        $manager->persist($theme2_13);
        
        $theme2_14 = new Theme();
        $theme2_14->setTemplate($template1);
        $theme2_14->setBackgroundColor('ffe500');
        $theme2_14->setPageColor('555000');
        $theme2_14->setTitleColor('000');
        $theme2_14->setParagraphColor('666');
        $theme2_14->setPrimaryColor('666');
        $theme2_14->setCssSource('2_15.css');
        $manager->persist($theme2_14);
        
        $theme2_15 = new Theme();
        $theme2_15->setTemplate($template1);
        $theme2_15->setBackgroundColor('00eeff');
        $theme2_15->setPageColor('555000');
        $theme2_15->setTitleColor('000');
        $theme2_15->setParagraphColor('666');
        $theme2_15->setPrimaryColor('666');
        $theme2_15->setCssSource('2_16.css');
        $manager->persist($theme2_15);


        $template1->setTheme($theme2_1);
        
        $block1_6 = new BlockTemplate();
        $block1_6->setTitle('Contacts');
        $block1_6->setType(BlockTemplate::TYPE_FIXED);
        $block1_6->setSlot($templateSlot2);
        $block1_6->setTemplate($template1);
        $block1_6->setHtmlSource('<div class="group item" class="item" data-block-id="{{block_data.id}}" data-block-type="0" data-is-draggable="false" data-is-editable="true" data-is-deletable="false"> <div class="group title" data-key="contact_title">{{ contact_title }}</div><div class="group content contacts"> <div> <i class="fa fa-map-marker" aria-hidden="true" ></i> <span data-key="location" data-placeholder="Location">{{location}}</span> </div><div> <i class="fa fa-phone" aria-hidden="true"></i> <span data-key="phone" data-placeholder="Phone number">{{phone}}</span> </div><div> <i class="fa fa-envelope" aria-hidden="true"></i> <span data-key="email" data-placeholder="Email address">{{email}}</span> </div></div></div>');
        $block1_6->setAvailableFields(json_encode(array(
            'contact_title' => 'Contacts',
            'location' => '',
            'phone' => '',
            'email' => ''
        )));
        $manager->persist($block1_6);

    }
}