<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Template;
use AppBundle\Entity\TemplateSlot;
use AppBundle\Entity\Block;
use AppBundle\Entity\User;
use AppBundle\Entity\CV;
use AppBundle\Entity\BlockData;

class LoadAllData implements FixtureInterface
{
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

        $block1_1 = new Block();
        $block1_1->setTitle('Personal info headline');
        $block1_1->setType(1);
        $block1_1->addTemplateSlot($templateSlot1_1);
        $block1_1->setHtmlSource('<div class="container"><div class="row"><div class="col-xs-12"><div id="top"><div class="big-title pull-left"><h1 class="title">{{ first_name }} <span>{{ last_name }}</span></h1><h3 class="subtitle">{{ title }}</h3></div><div class="contacts pull-right"><a href="mailto:{{ email }}">{{ email }}</a> <a href="tel:{{ phone }}">{{ phone }}</a></div><div class="clear"></div></div></div></div></div>');
        $block1_1->setAvailableFields(json_encode(array(
                'first_name',
                'last_name',
                'title',
                'email',
                'phone'
            )));
        $manager->persist($block1_1);

        $block1_2 = new Block();
        $block1_2->setTitle('Summary');
        $block1_2->setType(2);
        $block1_2->addTemplateSlot($templateSlot1_2);
        $block1_2->setHtmlSource('<div class="item"><h2 class="title"><i class="glyphicon glyphicon-user"></i><span>Summary</span></h2><p>{{ text }}</p>
            </div>');
        $block1_2->setAvailableFields(
            json_encode(array(
                'text'
            )));
        $manager->persist($block1_2);

        $block1_3 = new Block();
        $block1_3->setTitle('Experience');
        $block1_3->setType(3);
        $block1_3->addTemplateSlot($templateSlot1_2);
        $block1_3->setHtmlSource('<div class="item"><h2 class="title"><i class="glyphicon glyphicon-briefcase"></i><span>Experience</span></h2><ul class="timeline">{{ blocks|raw }}</ul></div>');
        $block1_3->setAvailableFields(
            json_encode(array(
                'blocks'
            )));
        $manager->persist($block1_3);

        $block1_3_1 = new Block();
        $block1_3_1->setParent($block1_3);
        $block1_3_1->setTitle('Experience entry');
        $block1_3_1->setType(3);
        $block1_3_1->setHtmlSource('<li><div class="icon"></div><div class="content"><div class="date">{{ date_from }} - {{ date_to }}</div><h3 class="position">{{ position }}</h3><h3 class="subtitle">{{ company }}</h3><h4>Redesigning</h4></div></li>');
        $block1_3_1->setAvailableFields(
            json_encode(array(
                'date_from',
                'date_to',
                'position',
                'company'
            )));
        $manager->persist($block1_3_1);

        $block1_4 = new Block();
        $block1_4->setTitle('Education');
        $block1_4->setType(3);
        $block1_4->addTemplateSlot($templateSlot1_2);
        $block1_4->setHtmlSource('<div class="item"><h2 class="title"><i class="glyphicon glyphicon-education"></i><span>Education</span></h2><ul class="timeline">{{ blocks|raw }}</ul></div>');
        $block1_4->setAvailableFields(
            json_encode(array(
                'blocks'
            )));
        $manager->persist($block1_4);

        $block1_4_1 = new Block();
        $block1_4_1->setParent($block1_4);
        $block1_4_1->setTitle('Education entry');
        $block1_4_1->setType(3);
        $block1_4_1->setHtmlSource('<li><div class="icon"></div><div class="content"><div class="date">{{ date_from }} - {{ date_to }}</div><h3 class="position">{{ position }}</h3><h3 class="subtitle">{{ company }}</h3><h4>Redesigning</h4></div></li>');
        $block1_4_1->setAvailableFields(
            json_encode(array(
                'date_from',
                'date_to',
                'position',
                'company'
            )));
        $manager->persist($block1_4_1);

        $block1_5 = new Block();
        $block1_5->setTitle('Skills');
        $block1_5->setType(3);
        $block1_5->addTemplateSlot($templateSlot1_3);
        $block1_5->setHtmlSource('<div class="item">
              <h2 class="title"><i class="glyphicon glyphicon-tasks"></i><span>Skills</span></h2>{{ blocks|raw }}</div>');
        $block1_5->setAvailableFields(
            json_encode(array(
                'blocks'
            )));
        $manager->persist($block1_5);

        $block1_5_1 = new Block();
        $block1_5_1->setParent($block1_5);
        $block1_5_1->setTitle('Skills/languages entry');
        $block1_5_1->setType(3);
        $block1_5_1->setHtmlSource('<div class="skills-group"><label>{{ title }}</label><ul class="skills">{% if skill > 0 %}{% for i in 1..skill %}<li class="completed"></li>{% endfor %}{% endif %}{% if skill < 10 %}{% for i in 1..(10-skill) %}<li></li>{% endfor %}{% endif %}</ul></div>');
        $block1_5_1->setAvailableFields(
            json_encode(array(
                'title',
                'skill'
            )));
        $manager->persist($block1_5_1);

        $block1_6 = new Block();
        $block1_6->setTitle('Languages');
        $block1_6->setType(3);
        $block1_6->addTemplateSlot($templateSlot1_3);
        $block1_6->setHtmlSource('<div class="item">
              <h2 class="title"><i class="glyphicon glyphicon-book"></i><span>Languages</span></h2>{{ blocks|raw }}</div>');
        $block1_6->setAvailableFields(
            json_encode(array(
                'blocks'
            )));
        $manager->persist($block1_6);

        $user1 = new User();        
        $user1->setEmail('testinis.meska@example.com');
        $user1->setPlainPassword('test');
        $user1->setEnabled(true);        
        $manager->persist($user1);

        $cv1 = new CV();
        $cv1->setUser($user1);
        $cv1->setTitle('My great cv');
        $cv1->setUrl('my_great_cv');
        $cv1->setTemplate($template1);
        $manager->persist($cv1);

        $blockData1_1 = new BlockData();
        $blockData1_1->setCV($cv1);
        $blockData1_1->setTemplateSlot($templateSlot1_1);
        $blockData1_1->setBlock($block1_1);
        $blockData1_1->setData(json_encode(array(
                'first_name' => 'Erikas',
                'last_name' => 'Mališauskas',
                'title' => 'UI/UX designer',
                'email' => 'erikas@malisauskas.lt',
                'phone' => '+370 (629) 26 815'
            )));
        $manager->persist($blockData1_1);

        $blockData1_2 = new BlockData();
        $blockData1_2->setCV($cv1);
        $blockData1_2->setTemplateSlot($templateSlot1_2);
        $blockData1_2->setBlock($block1_2);
        $blockData1_2->setData(json_encode(array(
                'text' => 'Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Vestibulum id ligula porta felis euismod semper. Nullam quis risus eget urna mollis ornare vel eu leo. Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
            )));
        $manager->persist($blockData1_2);

        $blockData1_3 = new BlockData();
        $blockData1_3->setCV($cv1);
        $blockData1_3->setTemplateSlot($templateSlot1_2);
        $blockData1_3->setBlock($block1_3);
        $blockData1_3->setData(json_encode(array(
                'blocks' => ''
            )));
        $manager->persist($blockData1_3);

        $blockData1_3_1 = new BlockData();
        $blockData1_3_1->setParent($blockData1_3);
        $blockData1_3_1->setCV($cv1);
        $blockData1_3_1->setBlock($block1_3_1);
        $blockData1_3_1->setData(json_encode(array(
                'date_from' => '2014 June',
                'date_to' => 'present',
                'position' => 'Senior UI/UX designer',
                'company' => 'MailerLite'
            )));
        $manager->persist($blockData1_3_1);

        $blockData1_3_2 = new BlockData();
        $blockData1_3_2->setParent($blockData1_3);
        $blockData1_3_2->setCV($cv1);
        $blockData1_3_2->setBlock($block1_3_1);
        $blockData1_3_2->setData(json_encode(array(
                'date_from' => '2014 June',
                'date_to' => 'present',
                'position' => 'Senior UI/UX designer',
                'company' => 'MailerLite'
            )));
        $manager->persist($blockData1_3_2);

        $blockData1_3_3 = new BlockData();
        $blockData1_3_3->setParent($blockData1_3);
        $blockData1_3_3->setCV($cv1);
        $blockData1_3_3->setBlock($block1_3_1);
        $blockData1_3_3->setData(json_encode(array(
                'date_from' => '2014 June',
                'date_to' => 'present',
                'position' => 'Senior UI/UX designer',
                'company' => 'MailerLite'
            )));
        $manager->persist($blockData1_3_3);

        $blockData1_4 = new BlockData();
        $blockData1_4->setCV($cv1);
        $blockData1_4->setTemplateSlot($templateSlot1_2);
        $blockData1_4->setBlock($block1_4);
        $blockData1_4->setData(json_encode(array(
                'blocks' => ''
            )));
        $manager->persist($blockData1_4);

        $blockData1_4_1 = new BlockData();
        $blockData1_4_1->setParent($blockData1_4);
        $blockData1_4_1->setCV($cv1);
        $blockData1_4_1->setBlock($block1_4_1);
        $blockData1_4_1->setData(json_encode(array(
                'date_from' => '2014 June',
                'date_to' => 'present',
                'position' => 'Senior UI/UX designer',
                'company' => 'MailerLite'
            )));
        $manager->persist($blockData1_4_1);

        $blockData1_4_2 = new BlockData();
        $blockData1_4_2->setParent($blockData1_4);
        $blockData1_4_2->setCV($cv1);
        $blockData1_4_2->setBlock($block1_4_1);
        $blockData1_4_2->setData(json_encode(array(
                'date_from' => '2014 June',
                'date_to' => 'present',
                'position' => 'Senior UI/UX designer',
                'company' => 'MailerLite'
            )));
        $manager->persist($blockData1_4_2);

        $blockData1_4_3 = new BlockData();
        $blockData1_4_3->setParent($blockData1_4);
        $blockData1_4_3->setCV($cv1);
        $blockData1_4_3->setBlock($block1_4_1);
        $blockData1_4_3->setData(json_encode(array(
                'date_from' => '2014 June',
                'date_to' => 'present',
                'position' => 'Senior UI/UX designer',
                'company' => 'MailerLite'
            )));
        $manager->persist($blockData1_4_3);

        $blockData1_5 = new BlockData();
        $blockData1_5->setCV($cv1);
        $blockData1_5->setTemplateSlot($templateSlot1_3);
        $blockData1_5->setBlock($block1_5);
        $blockData1_5->setData(json_encode(array(
                'blocks' => ''
            )));
        $manager->persist($blockData1_5);

        $blockData1_5_1 = new BlockData();
        $blockData1_5_1->setParent($blockData1_5);
        $blockData1_5_1->setCV($cv1);
        $blockData1_5_1->setBlock($block1_5_1);
        $blockData1_5_1->setData(json_encode(array(
                'title' => 'HTML/CSS',
                'skill' => '8'
            )));
        $manager->persist($blockData1_5_1);

        $blockData1_5_2 = new BlockData();
        $blockData1_5_2->setParent($blockData1_5);
        $blockData1_5_2->setCV($cv1);
        $blockData1_5_2->setBlock($block1_5_1);
        $blockData1_5_2->setData(json_encode(array(
                'title' => 'Photoshop',
                'skill' => '8'
            )));
        $manager->persist($blockData1_5_2);

        $blockData1_5_3 = new BlockData();
        $blockData1_5_3->setParent($blockData1_5);
        $blockData1_5_3->setCV($cv1);
        $blockData1_5_3->setBlock($block1_5_1);
        $blockData1_5_3->setData(json_encode(array(
                'title' => 'Ilustrator',
                'skill' => '4'
            )));
        $manager->persist($blockData1_5_3);

        $blockData1_5_4 = new BlockData();
        $blockData1_5_4->setParent($blockData1_5);
        $blockData1_5_4->setCV($cv1);
        $blockData1_5_4->setBlock($block1_5_1);
        $blockData1_5_4->setData(json_encode(array(
                'title' => 'NodeJS',
                'skill' => '2'
            )));
        $manager->persist($blockData1_5_4);

        $blockData1_6 = new BlockData();
        $blockData1_6->setCV($cv1);
        $blockData1_6->setTemplateSlot($templateSlot1_3);
        $blockData1_6->setBlock($block1_6);
        $blockData1_6->setData(json_encode(array(
                'blocks' => ''
            )));
        $manager->persist($blockData1_6);

        $blockData1_6_1 = new BlockData();
        $blockData1_6_1->setParent($blockData1_6);
        $blockData1_6_1->setCV($cv1);
        $blockData1_6_1->setBlock($block1_5_1);
        $blockData1_6_1->setData(json_encode(array(
                'title' => 'Lithuanian',
                'skill' => '10'
            )));
        $manager->persist($blockData1_6_1);

        $blockData1_6_2 = new BlockData();
        $blockData1_6_2->setParent($blockData1_6);
        $blockData1_6_2->setCV($cv1);
        $blockData1_6_2->setBlock($block1_5_1);
        $blockData1_6_2->setData(json_encode(array(
                'title' => 'English',
                'skill' => '8'
            )));
        $manager->persist($blockData1_6_2);

        $blockData1_6_3 = new BlockData();
        $blockData1_6_3->setParent($blockData1_6);
        $blockData1_6_3->setCV($cv1);
        $blockData1_6_3->setBlock($block1_5_1);
        $blockData1_6_3->setData(json_encode(array(
                'title' => 'Russian',
                'skill' => '7'
            )));
        $manager->persist($blockData1_6_3);

        $manager->flush();
    }
}