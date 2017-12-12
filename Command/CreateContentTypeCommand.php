<?php
/**
 * File containing the CreateContentTypeCommand class.
 *
 * @copyright Copyright (C) Azimutec. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace Azimutec\KikundiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;

class CreateContentTypeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName( 'azimutec:kikundi:create_content_type' )->setDefinition(
            array(
                new InputArgument( 'group_identifier', InputArgument::REQUIRED, 'a content type group identifier' ),
                new InputArgument( 'identifier', InputArgument::REQUIRED, 'a content type identifier' )
            )
        );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        /** @var $repository \eZ\Publish\API\Repository\Repository */
        $repository = $this->getContainer()->get( 'ezpublish.api.repository' );
        $contentTypeService = $repository->getContentTypeService();

        $repository->setCurrentUser( $repository->getUserService()->loadUser( 14 ) );

        // fetch command line arguments
        $groupIdentifier = $input->getArgument( 'group_identifier' );
        $contentTypeIdentifier = $input->getArgument( 'identifier' );

        try
        {
            $contentTypeGroup = $contentTypeService->loadContentTypeGroupByIdentifier( $groupIdentifier );
        }
        catch ( \eZ\Publish\API\Repository\Exceptions\NotFoundException $e )
        {
            $output->writeln( "<error>Content type group with identifier '" . $groupIdentifier . "' not found</error>" );
            return;
        }

        // instantiate a ContentTypeCreateStruct with the given content type identifier and set parameters
        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct( $contentTypeIdentifier );

        // Main language
        $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';

        // set names for the content type
        $contentTypeCreateStruct->names = array(
            'eng-GB' => $contentTypeIdentifier,
            // 'eng-GB' => $contentTypeIdentifier . 'eng-GB',
            // 'fre-FR' => $contentTypeIdentifier,
            // 'fre-FR' => $contentTypeIdentifier . 'fre-FR',
            // 'ger-DE' => $contentTypeIdentifier . 'ger-DE',
        );

        // set description for the content type
        $contentTypeCreateStruct->descriptions = array(
            'eng-GB' => 'Description for ' . $contentTypeIdentifier . ' [eng-GB]',
            // 'fre-FR' => 'Description pour ' . $contentTypeIdentifier . ' [fre-FR]',
            // 'ger-DE' => 'Description for ' . $contentTypeIdentifier . ' [ger-DE]',
        );


        switch ($contentTypeIdentifier)
        {
            case "kik_article1":
                //$this->createKikArticle1($contentTypeIdentifier, );
                break;
            
            case "kik_folder1":
                $this->createKikFolder1();
                break;
            
            case "kik_image1":
                $this->createKikImage1();
                break;
            
            case "kik_image1_folder":
                $this->createKikImage1Folder();
                break;
            
            case "kik_file1":
                $this->createKikImage1();
                break;
            
            case "kik_file1_folder":
                $this->createKikImage1Folder();
                break;
            
            default:
                $output->writeln( "<error>Content type with identifier '" . $contentTypeIdentifier . "' is not allowed</error>" );
                return;
                break;
            
        }

        try
        {
            $contentTypeDraft = $contentTypeService->createContentType( $contentTypeCreateStruct, array( $contentTypeGroup ) );
            $contentTypeService->publishContentTypeDraft( $contentTypeDraft );
            $output->writeln( "<info>Content type created '" . $contentTypeIdentifier . "' with ID '" . $contentTypeDraft->id . "'</info>" );
        }
        catch ( \eZ\Publish\API\Repository\Exceptions\UnauthorizedException $e )
        {
            $output->writeln( "<error>" . $e->getMessage() . "</error>" );
        }
        catch ( \eZ\Publish\API\Repository\Exceptions\ForbiddenException $e )
        {
            $output->writeln( "<error>" . $e->getMessage() . "</error>" );
        }
    }

    /**
     * Prints out the location name, and recursively calls itself on each its children
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param OutputInterface $output
     *
     * @return ContentTypeCreateStruct $contentTypeCreateStruct
     */
    private function createKikArticle1( string $contentTypeIdentifier, ContentTypeService $contentTypeService, ContentTypeCreateStruct $contentTypeCreateStruct, OutputInterface $output )
    {
        $output->writeln( " . creating content type with identifier '" . $contentTypeIdentifier . "':" );

        // We set the Content Type naming pattern to the title's value
        $contentTypeCreateStruct->nameSchema = '<title>';

        // add a TextLine Field with identifier 'title'
        $titleFieldCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct( 'title', 'ezstring' );
        $titleFieldCreateStruct->names = array( 'eng-GB' => 'Title'/*, 'ger-DE' => 'Titel'*/ );
        $titleFieldCreateStruct->descriptions = array( 'eng-GB' => 'The Title'/*, 'ger-DE' => 'Der Titel'*/ );
        $titleFieldCreateStruct->fieldGroup = 'content';
        $titleFieldCreateStruct->position = 10;
        $titleFieldCreateStruct->isTranslatable = true;
        $titleFieldCreateStruct->isRequired = true;
        $titleFieldCreateStruct->isSearchable = true;
        $contentTypeCreateStruct->addFieldDefinition( $titleFieldCreateStruct );
        $output->writeln( "   . field 'title'" );

        // add a TextLine Field body field
        $bodyFieldCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct( 'body', 'ezstring' );
        $bodyFieldCreateStruct->names = array( 'eng-GB' => 'Body'/*, 'ger-DE' => 'Text'*/ );
        $bodyFieldCreateStruct->descriptions = array( 'eng-GB' => 'Description for Body'/*, 'ger-DE' => 'Beschreibung Text'*/ );
        $bodyFieldCreateStruct->fieldGroup = 'content';
        $bodyFieldCreateStruct->position = 20;
        $bodyFieldCreateStruct->isTranslatable = true;
        $bodyFieldCreateStruct->isRequired = true;
        $bodyFieldCreateStruct->isSearchable = true;
        $contentTypeCreateStruct->addFieldDefinition( $bodyFieldCreateStruct );
        $output->writeln( "   . field 'body'" );

        return $contentTypeCreateStruct;
    }

    /**
     * Prints out the location name, and recursively calls itself on each its children
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @param OutputInterface $output
     */
    private function createKikFolder1( )
    {
    }

    /**
     * Prints out the location name, and recursively calls itself on each its children
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @param OutputInterface $output
     */
    private function createKikImage1( )
    {
    }

    /**
     * Prints out the location name, and recursively calls itself on each its children
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @param OutputInterface $output
     */
    private function createKikImage1Folder( )
    {
    }
}