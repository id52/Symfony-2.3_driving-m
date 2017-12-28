<?php

namespace My\AppBundle\Controller;

use Liip\ImagineBundle\Controller\ImagineController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImagineController extends BaseController
{
    /** @var \Doctrine\ORM\EntityManager */
    public $em;

    public function photoAction(Request $request, $path, $filter)
    {
        $targetPath = $this->cacheManager->resolve($request, $path, $filter);
        if ($targetPath instanceof Response) {
            return $targetPath;
        }

        $image = $this->dataManager->find($filter, $path);

        $filterConfig = $this->filterManager->getFilterConfiguration();
        $config = $filterConfig->get($filter);

        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array(
            'photo' => substr($path, strrpos($path, DIRECTORY_SEPARATOR) + 1),
        ));
        if (!$user) {
            throw new NotFoundHttpException('Photo for path "'.$path.'" not found.');
        }

        $coords = $user->getPhotoCoords();
        $config['filters']['crop'] = array(
            'start' => array($coords['x'], $coords['y']),
            'size'  => array($coords['w'], $coords['h']),
        );

        $filterConfig->set($filter, $config);

        $response = $this->filterManager->get($request, $filter, $image, $path);

        if ($targetPath) {
            $response = $this->cacheManager->store($response, $targetPath, $filter);
        }

        return $response;
    }
}
