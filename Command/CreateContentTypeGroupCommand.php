<?php
/**
 * File containing the CreateContentTypeGroupCommand class.
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

class CreateContentTypeGroupCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName( 'azimutec:kikundi:create_content_type_group' )->setDefinition(
            array(
                new InputArgument( 'group_identifier', InputArgument::REQUIRED, 'a content type group identifier' )
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

        // instantiate a ContentTypeGroupCreateStruct with the given content type group identifier and set parameters
        $contentTypeGroupCreateStruct = $contentTypeService->newContentTypeGroupCreateStruct( $groupIdentifier );

        try
        {
            $contentTypeGroup = $contentTypeService->createContentTypeGroup( $contentTypeGroupCreateStruct );
            $output->writeln( "<info>Content type group created '" . $groupIdentifier . "' with ID '" . $contentTypeGroup->id . "'</info>" );
        }
        catch ( \eZ\Publish\API\Repository\Exceptions\UnauthorizedException $e )
        {
            $output->writeln( "<error>" . $e->getMessage() . "</error>" );
        }
        catch ( \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException $e )
        {
            $output->writeln( "<error>" . $e->getMessage() . "</error>" );
        }
    }
}