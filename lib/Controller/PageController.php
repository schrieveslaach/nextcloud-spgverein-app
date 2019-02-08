<?php

namespace OCA\SPGVerein\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

class PageController extends Controller
{

    public function __construct($AppName, IRequest $request)
    {
        parent::__construct($AppName, $request);
    }

    /**
     * CAUTION: the @Stuff turns off security checks; for this page no admin is
     *          required and no CSRF check. If you don't know what CSRF is, read
     *          it up in the docs or you might create a security hole. This is
     *          basically the only required method to add this exemption, don't
     *          add it to any other method if you don't exactly know what it does
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index()
    {
        $response = new TemplateResponse('spgverein', 'index');
        $csp = new ContentSecurityPolicy();

        $csp->allowInlineScript(true)
            ->allowInlineStyle(true)
            ->allowEvalScript(true);

        $csp->addAllowedScriptDomain('*')
            ->addAllowedStyleDomain('*')
            ->addAllowedFontDomain('*')
            ->addAllowedImageDomain('*')
            ->addAllowedConnectDomain('*')
            ->addAllowedMediaDomain('*')
            ->addAllowedObjectDomain('*')
            ->addAllowedFrameDomain('*')
            ->addAllowedChildSrcDomain('*');
        $response->setContentSecurityPolicy($csp);
        return $response;
    }

}
