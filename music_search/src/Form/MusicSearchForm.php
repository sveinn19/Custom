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
                'all' => t('All'),
                'records' => t('Records'),
                'artist' => t('Artists'),
                'labels' => t('Labels'),
                'song' => t('Songs'),
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

        $redirect = new RedirectResponse(Url::fromUserInput('/musicsearch')->toString());
        $redirect->send();
        
    }

 }