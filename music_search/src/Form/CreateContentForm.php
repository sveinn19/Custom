<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Form for searching music.
 */

 class CreateContentForm extends FormBase{

    public function __construct($spotify_service, $discogs_service) {  
        $this->spotify_service = $spotify_service;
        $this->discogs_service = $discogs_service;
        }
    
        /** * {@inheritdoc} 
         * */
    public static function create(ContainerInterface $container) { 
         return new static(    
           $container->get('spotify_lookup.spotify_lookup'),
           $container->get('discogs_lookup.discogs_lookup')
           );
         }

    public function getFormId(){
        return 'create_content_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state){
        $result = $_SESSION['s1'][$_SESSION['s2'] . 's']['items'];//[$_SESSION['con']];
        $_SESSION['result'] = $result;

        $this->get_inputs();

        $form['nafn'] = array(
            '#type' => 'radios',
            '#title' => 'Choose name: ',
            '#options' => $this->getNames($_SESSION['spot-res'], $_SESSION['dc-res']),
            //'#suffix' => "<h2>Spotify</h2><pre>". print_r($_SESSION['spot-res'], true)."</pre>",
            //'#prefix' => "<h2>Discogs</h2><pre>". print_r($_SESSION['dc-res'], true)."</pre>",
            //'#suffix' => "<pre>".print_r($
        );

        $form['image'] = array(
            '#type' => 'radios',
            '#title' => 'Choose image: ',
            '#options' => $this->getImages($_SESSION['spot-res'], $_SESSION['dc-res']),
            //'#suffix' => "
        );

        $form['site'] = array(
            '#type' => 'radios',
            '#title' => 'Choose website: ',
            '#options' => $this->getSites($_SESSION['spot-res'], $_SESSION['dc-res']),
            //'#suffix' => "
        );

        $form['members'] = array(
            '#type' => 'checkboxes',
            '#title' => 'Add members: ',
            '#options' => $this->getMembers($_SESSION['dc-res']),
            //'#suffix' => "
        );

        $form['description'] = array(
            '#type' => 'radios',
            '#title' => 'Choose description: ',
            '#options' => $this->getDescr($_SESSION['dc-res']),
            //'#suffix' => "
        );

        $form['text'] = array(
            '#type' => 'checkboxes',
            '#title' => 'Choose what to create',
            '#suffix' => "<h2>Spotify</h2><pre>". print_r($_SESSION['spot-res'], true)."</pre>",
            '#prefix' => "<h2>Discogs</h2><pre>". print_r($_SESSION['dc-res'], true)."</pre>",
            //'#suffix' => "<pre>".print_r($result, true)."</pre>",
        );

        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Submit'),

        );

        return $form;
    }

    private function getDescr($discogs){
        $option = [];
        foreach($discogs as $key => $value){
            $option[$value['profile']] = $value['profile'] . '  (Discogs)';
        }
        return $option;
    }

    private function getNames($spotify, $discogs){
        $option = [];
        foreach($spotify as $key => $value){
            if (!isset($option[$value['name']])){
                $option[$value['name']] = t($value['name']).t('  (Spotify)');
            }
        }

        foreach($discogs as $key => $value){
            if (!isset($option[$value['name']])){
                $option[$value['name']] = ($value['name']).t('  (Discogs)');
            }
            if (!isset($option[$value['realname']])){
                $option[$value['realname']] = ($value['realname']).t('  (Discogs)');
            }
        }


        return $option;
    }

    private function getImages($spotify, $discogs){
        $option = [];
        foreach($spotify as $key => $value){
            $option[$value['images'][0]['url']] = '<img src='. '"' . $value['images'][0]['url'] . '" width="100" > (  Spotify)';
            
        }

        foreach($discogs as $key => $value){
            $option[$value['cover_image']] = '<img src='. '"' . $value['cover_image'] . '" width="200"> (  Discogs)';
        }


        return $option;

    }


    private function getSites($spotify, $discogs){
        $option = [];
        foreach($spotify as $key => $value){
            $option[$value['external_urls']['spotify']] = $value['external_urls']['spotify'];
            
        }

        foreach($discogs as $key => $value){
            $c = 0;
            foreach($value['urls'] as $key2 => $value2){
                if($c !== 5){
                    $option[$value2] = $value2;
                }
                $c++;

            }
        }


        return $option;
    }

    private function getMembers($discogs){
        $option = [];
        foreach($discogs as $key => $value){
            if (isset($value['members'])){
                foreach($value['members'] as $key2 => $value2){
                    $option[$value2['name']] = $value2['name'];
                }
            }
            
        }
        return $option;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        \Drupal::messenger()->addMessage(t('CreateContent  ') . print_r($form_state->getValues(), true));

        // $result = $_SESSION['spot-res'][0];
        // $url = substr($result['external_urls']['spotify'], 8, strlen($result['external_urls']['spotify']));

        $node = Node::create([
            'type'  => 'listamadur',
            'title' =>  $form_state->getValue('nafn'), //$result['name'],
            'body' => $form_state->getValue('description'),
          ]);
          $node->field_nafn->value = $form_state->getValue('nafn');
          $node->set('field_vefsida_listamanns', [
            'uri' => $form_state->getValue('site'),//$result['external_urls']['spotify'],
            'title' =>$form_state->getValue('site'),// $result['external_urls']['spotify'],
            'options' => [],
          ]);
        
          $node->save();
    }

    private function get_inputs(){
        $temp_sp = [];
        $temp_dc = [];

        foreach($_SESSION['con'] as $key => $value){
            if ($value !== 0){
                //$temp[$key] = [substr($value, -2), substr($value, 0, -2)];
                if (substr($value, -2) == 'sp'){
                    // if($_SESSION['s2'] == 'album'){
                    //     array_push($temp_sp, $_SESSION['s1'][(int)substr($value, 0, -2)]);
                    // }else {
                    array_push($temp_sp, $_SESSION['s1'][$_SESSION['s2'] . 's']['items'][(int)substr($value, 0, -2)]);
                    
                }
                else {
                    $tmp = $this->discogs_service->_discogs_api_get_query($_SESSION['d1']['results'][(int)substr($value, 0, -2)]['resource_url']);
                    $tmp['cover_image'] = $_SESSION['d1']['results'][(int)substr($value, 0, -2)]['cover_image'];
                    array_push($temp_dc, $tmp);
                }
            }
        }

        $_SESSION['spot-res'] = $temp_sp;
        $_SESSION['dc-res'] = $temp_dc;
    }

 }