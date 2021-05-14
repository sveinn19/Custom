<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
* Form for searching music.
 */

 class MusicSearchForm extends FormBase{

    public function getFormId(){
        return 'music_search_form1';
    }

    public function buildForm(array $form, FormStateInterface $form_state){
        $form['search_in']['type_select'] = array(
            '#type' => 'select',
            '#title' => $this->t('Select content to search for.'),
            '#options' => array(
                'album' => t('Album'),
                'artist' => t('Artists'),
                'track' => t('Songs'),
            ),
        );

        $form['sstring'] = array(
            '#type' => 'textfield',
            '#title' => $this->t("Searchbar"),

        );

        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Submit'),

        );

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        \Drupal::messenger()->addMessage(t('Searching for ') . $form_state->getValue('sstring') . t(' in ').  $form_state->getValue('type_select'));


        $url = \Drupal\Core\Url::fromRoute('music_search.search')
          ->setRouteParameters(array('type'=>$form_state->getValue('type_select'),'string'=>$form_state->getValue('sstring')));

        $form_state->setRedirectUrl($url);
        
    }

 }