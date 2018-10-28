<?php

namespace Stylex\Loader;

use Symfony\Component\Yaml\Yaml;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

class DirectoryEntityLoader
{
    public function load($path)
    {
        if (!is_dir($path)) {
            throw new RuntimeException("Invalid path: " . $path);
        }

        $it = new RecursiveDirectoryIterator($path);

        $display = ['yaml'];

        $config = [];

        foreach (new RecursiveIteratorIterator($it) as $filename) {
            
            $shortFilename = substr($filename, strlen($path)+1);

            if (is_file($filename)) {
                $info = pathinfo($filename);
                switch ($info['extension']) {
                    case 'yaml':
                    case 'yml':
                        $yaml = file_get_contents($filename);
                        $edata = Yaml::parse($yaml);
                        $type = $edata['type'] ?? null;
                        if ($type) {
                            $id = $edata['id'] ?? basename($filename, '.yaml');
                            if (!isset($edata['id'])) {
                                $edata['id'] = $id;
                            }
                            $edata = $this->fixKeyIds($id, $edata);

                            if (isset($config['entity'][$id])) {
                                throw new RuntimeException("Duplicate entity id: " . $id);
                            }
                            $config[$id] = $edata;
                            // $config['type'][$type][$id] = $edata;
                            
                        }
                        break;
                }
            }
        }
        // print_r($entities);
        $config = $this->fixReferences($config, $config);

        $guide = $this->postProcess($config);
        return $guide;
    }

    private function fixKeyIds($id, array $input)
    {
        $return = array();
        foreach ($input as $key => $value) {
            if ($key[0] == '~') {
                $key = $id . $key;
            }
    
            if (is_array($value))
                $value = $this->fixKeyIds($id, $value); 
    
            $return[$key] = $value;
        }
        return $return;
    }

    private function fixReferences($config, array $input)
    {
        $return = array();
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                if ($value[0] == '$') {
                    $refKey = substr($value, 1);
                    if (!isset($config[$refKey])) {
                        throw new RuntimeException("Undefined reference: " . $refKey);
                    }
                    $value = $config[$refKey];
                }
            }
    
            if (is_array($value))
                $value = $this->fixReferences($config, $value); 
    
            $return[$key] = $value;
        }
        return $return;
    }

    private function postProcess($entities)
    {
        $guideId = null;
        foreach ($entities as $id=>$entity) {
            if ($entity['type']=='guide') {
                $guideId = $id;
            }
        }
    
        

        foreach ($entities as $id => &$entity) {
            if ($entity['type']=='category') {
                if (!isset($entity['rules'])) {
                    $entity['rules'] = [];
                }
                $entities[$guideId]['categories'][$id] = $entity;
            }
        }

        foreach ($entities as $id => &$entity) {
            if ($entity['type']=='rule') {
                $categoryId = $entity['category']['id'] ?? null;
                $category = &$entities[$categoryId];
                
                $entities[$guideId]['categories'][$categoryId]['rules'][$entity['id']] = $entity;
                unset($entities[$id]['category']);
            }
        }


        $guide = $entities[$guideId];
        return $guide;
    }
}