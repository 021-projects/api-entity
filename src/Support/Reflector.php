<?php

namespace O21\ApiEntity\Support;

class Reflector
{
    public static function importedClassesOf(\ReflectionClass $class): array
    {
        $code = file_get_contents($class->getFileName());
        $tokens = \PhpToken::tokenize($code);

        $imports = [];
        $collecting = false;
        $classFound = false;
        $import = '';

        foreach ($tokens as $token) {
            if ($token->is(T_CLASS)) {
                $classFound = true;
                break; // Stop parsing once we reach the class definition
            }

            if (!$classFound && $token->is(T_USE)) {
                $collecting = true;
                $import = '';
                continue;
            }

            if ($collecting) {
                if ($token->is(T_STRING) || $token->is(T_NS_SEPARATOR)) {
                    $import .= $token->text;
                } elseif ($token->text === ',') {
                    $imports[] = trim($import);
                    $import = ''; // Reset for multiple imports
                } elseif ($token->text === ';') {
                    $imports[] = trim($import);
                    $collecting = false;
                }
            }
        }

        return $imports;
    }
}
