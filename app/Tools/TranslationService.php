<?php

namespace App\Tools;

use Exception;
use Illuminate\Support\Facades\Http;
use PhpParser\Node\Stmt\Catch_;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationService
{
    private $translator;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->translator = new GoogleTranslate();
    }

    public function detect_lang(string $text){
        // Detecing language
        $this->translator->setTarget('en');
        $this->translator->translate(strtok($text, ' '));
        return $this->translator->getLastDetectedSource();
    }

    public function translate(string $text , $source_lang){

        $this->translator->setSource($source_lang);
        if($source_lang == 'en'){
            $this->translator->setTarget('ar');
        }else{
            $this->translator->setTarget('en');
        }
        try{
            return $this->translator->translate($text);
        }catch(Exception $e){
            return '*Exception*';
        }
    }

    public function translate_inputs(array $variables): array {
        $array = [];
        foreach($variables as $key=>$item){
            if($this->detect_lang($item) == 'en'){
                $array[$key] = $item;
                $array[$key.'_ar'] = $this->translate($item , 'en');
            }else{
                $array[$key.'_ar'] = $item;
                $array[$key] = $this->translate($item , 'ar');
            }
        }
        return $array;
    }
}
