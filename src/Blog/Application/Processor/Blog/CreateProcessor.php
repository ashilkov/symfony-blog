<?php
/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Processor\Blog;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Resource\Blog as BlogResource;
use App\Blog\Domain\Model\Blog as BlogDomain;
use App\Blog\Domain\Model\BlogUser;
use App\Blog\Domain\Repository\BlogRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;


class CreateProcessor implements ProcessorInterface
{

    public function __construct(
        private BlogRepositoryInterface $blogs,
        private BlogHydrator $hydrator,
        private Security $security,
    ) {
    }


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): BlogResource
    {
        $blog = new BlogDomain();
        if ($data->name !== null) {
            $blog->setName($data->name);
        }
        if ($data->description !== null) {
            $blog->setDescription($data->description);
        }

        $user = $this->security->getUser();
        if ($user === null) {
            throw new AccessDeniedException("You are not allowed to create a blog.");
        }
        $blog->addBlogUser(new BlogUser($blog, $user, "ROLE_ADMIN"));

        $this->blogs->save($blog, true);

        return $this->hydrator->hydrate($blog);
    }
}
