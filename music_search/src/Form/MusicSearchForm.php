<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;

/**
* Form for searching music.
 */

 class MusicSearchForm extends FormBase{

    public function getFormId(){
        return 'music_search_form1';
    }

    public function buildForm(array $form, FormStateInterface $form_state){
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
        \Drupal::messenger()->addMessage($form_state->getValue('sstring'));

    }



 }