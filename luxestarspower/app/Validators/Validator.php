<?php
namespace App\Validators;

class Validator {
    private $errors = [];
    
    public function validate($data, $rules) {
        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);
            $value = $data[$field] ?? null;
            
            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }
        
        return empty($this->errors);
    }
    
    private function applyRule($field, $value, $rule) {
        if ($rule === 'required' && empty($value)) {
            $this->errors[$field][] = "$field is required";
        }
        
        if (strpos($rule, 'min:') === 0) {
            $min = intval(substr($rule, 4));
            if (strlen($value) < $min) {
                $this->errors[$field][] = "$field must be at least $min characters";
            }
        }
        
        if (strpos($rule, 'max:') === 0) {
            $max = intval(substr($rule, 4));
            if (strlen($value) > $max) {
                $this->errors[$field][] = "$field must not exceed $max characters";
            }
        }
        
        if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "$field must be a valid email";
        }
        
        if ($rule === 'numeric' && !is_numeric($value)) {
            $this->errors[$field][] = "$field must be numeric";
        }
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getFirstError($field) {
        return $this->errors[$field][0] ?? null;
    }
    
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
}
