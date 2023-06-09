<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Repository\CategoryRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use App\Service\VersioningService;


class ArticleController extends AbstractController
{


    #[Route('/api/articles/{id}', name: 'detailArticle', methods: ['GET'])]
    public function detailArticle(int $id, SerializerInterface $serializer,
        ArticleRepository $articleRepository, VersioningService $versioningService): JsonResponse
    {
        $article = $articleRepository->find($id);

        if ($article instanceof Article) {

            $version = $versioningService->getVersion();
            $context = SerializationContext::create()->setGroups(['getDetailArticle']);
            $context->setVersion($version);

            $jsonArticle = $serializer->serialize($article, 'json', $context);
            return new JsonResponse($jsonArticle, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
   }

    #[Route('/api/articles/{page}/{limit}', requirements: ['page' => '\d+', 'limit' => '\d+'], name: 'listArticle', methods: ['GET'])]
    public function listArticle(ArticleRepository $articleRepository, SerializerInterface $serializer,
        TagAwareCacheInterface $cachePool, VersioningService $versioningService, int $page = 0, int $limit = 0 ): JsonResponse
    {
        $articleList = $articleRepository->findAllWithPagination($page, $limit);

        $idCache = "getAllArticles-" . $page . "-" . $limit;
        $jsonArticleList = $cachePool->get($idCache, function (ItemInterface $item) use ($articleRepository, $page, $limit, $serializer) {
            $item->tag("articlesCache");
            $articleList = $articleRepository->findAllWithPagination($page, $limit);
            $context = SerializationContext::create()->setGroups(['getArticles']);
            return $serializer->serialize($articleList, 'json', $context);
        });
        return new JsonResponse($jsonArticleList, Response::HTTP_OK, [], true);
    }


   #[Route('/api/articles/{id}', name: 'deleteArticle', methods: ['DELETE'])]
   public function deleteArticle(Article $article, ArticleRepository $articleRepository): JsonResponse
   {
       $articleRepository->remove($article, true);
       return new JsonResponse(null, Response::HTTP_NO_CONTENT);
   }

    #[Route('/api/articles', name:"createArticle", methods: ['POST'])]
    public function createArticle(Request $request, SerializerInterface $serializer,
        ArticleRepository $articleRepository, UrlGeneratorInterface $urlGenerator,
        CategoryRepository $categoryRepository, ValidatorInterface $validator ): JsonResponse
    {
        /**  @var Article $article */
        $article = $serializer->deserialize($request->getContent(), Article::class, 'json');

        // Category recovery
        $content = $request->toArray();
        $idCategory = $content['idCategory'] ?? -1;
        $article->setCategory($categoryRepository->find($idCategory));

        // We check for errors
        $errors = $validator->validate($article);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $articleRepository->save($article, true);

        // serialized created object
        $context = SerializationContext::create()->setGroups(['getArticles']);
        $jsonArticle = $serializer->serialize($article, 'json', $context);
        $location = $urlGenerator->generate('detailArticle', ['id' => $article->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        // information about created object in the header
        return new JsonResponse($jsonArticle, Response::HTTP_CREATED, ["Location" => $location], true);
   }

    #[Route('/api/articles/{id}', name:"updateArticle", methods: ['PUT'])]
    public function updateArticle(Request $request, SerializerInterface $serializer,
        Article $currentArticle, UrlGeneratorInterface $urlGenerator,
        CategoryRepository $categoryRepository, ArticleRepository $articleRepository ): JsonResponse
    {
        /**  @var Article $updateArticle */
        $updateArticle = $serializer->deserialize( $request->getContent(), Article::class, 'json' );

        /** @todo JMS\Serializer : Deserialize on existing objects */
        $currentArticle->setLabel($updateArticle->getLabel());
        $currentArticle->setColor($updateArticle->getColor());
        $currentArticle->setPrice($updateArticle->getPrice());
        $currentArticle->setValidated($updateArticle->isValidated());

        // Category recovery
        $content = $request->toArray();
        $idCategory = $content['idCategory'] ?? -1;

        $currentArticle->setCategory($categoryRepository->find($idCategory));
        $articleRepository->save($currentArticle, true);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
   }


}
