<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Version;

use phpDocumentor\DomainModel\Documentation\DocumentGroup\Definition\Factory;
use phpDocumentor\DomainModel\Documentation\DocumentGroup\DocumentGroupFormat;
use phpDocumentor\DomainModel\Version\Definition;
use phpDocumentor\Exception;
use phpDocumentor\DomainModel\Documentation\DocumentGroup\Definition as DocumentGroupDefinition;
use phpDocumentor\DomainModel\Version\Number;

/**
 * Factory for Version definition.
 * Will use the registered factories to create the configured DocumentGroup\Definitions.
 */
class DefinitionFactory implements \phpDocumentor\DefinitionFactory
{
    /**
     * @var Factory[]
     */
    private $documentGroupDefinitionFactories;

    /**
     * Creates a full provisioned version definition
     *
     * @param array $options
     * @return Definition
     */
    public function create(array $options)
    {
        $documentGroups = $this->createDocumentGroupDefinitions($options);

        return new Definition(
            new Number($options['version']),
            $documentGroups
        );
    }

    /**
     * creates a set of DocumentGroups as configured in the options.
     *
     * @param array $options
     *
     * @return DocumentGroupDefinition[]
     * @throws Exception
     */
    private function createDocumentGroupDefinitions(array $options)
    {
        $documentGroups = array();

        foreach ($options as $documentGroupType => $documentGroupOptions) {
            if (is_array($documentGroupOptions)) {
                $factory = $this->findFactory($documentGroupType, $documentGroupOptions['format']);
                $documentGroups[] = $factory->create($documentGroupOptions);
            }
        }

        return $documentGroups;
    }

    /**
     * @param string $type
     * @param string $format
     *
*@return Factory
     * @throws Exception
     */
    private function findFactory($type, $format)
    {
        if (isset($this->documentGroupDefinitionFactories[$type][$format])) {
            return $this->documentGroupDefinitionFactories[$type][$format];
        }

        throw new Exception(sprintf(
            'No factory registered for document group %s with format %s',
            $type,
            $format
        ));
    }

    /**
     * Register a factory for later usage for a given type and format.
     * Will override registered factories.
     * The combination of type and format will identify a certain documentGroup
     *
     * @param string $type
     * @param DocumentGroupFormat $format
     * @param Factory $factory
     */
    public function registerDocumentGroupDefinitionFactory(
        $type,
        DocumentGroupFormat $format,
        Factory $factory
    ) {
        $this->documentGroupDefinitionFactories[$type][(string)$format] = $factory;
    }
}
