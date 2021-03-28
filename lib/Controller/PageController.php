<?php

namespace OCA\SPGVerein\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IURLGenerator;

class PageController extends Controller
{

    /** @var IURLGenerator */
    private $urlGenerator;

    public function __construct($AppName, IRequest $request, IURLGenerator $urlGenerator)
    {
        parent::__construct($AppName, $request);
        $this->urlGenerator = $urlGenerator;
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
    public function index() {
        $response = new TemplateResponse('spgverein', 'index');
        $csp = new ContentSecurityPolicy();
        $csp->addAllowedObjectDomain('*');
        $response->setContentSecurityPolicy($csp);
        return $response;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function redirectToMember($club, $member) {
        $url = $this->urlGenerator->linkToRoute('spgverein.page.index');
        return new RedirectResponse("${url}/#/clubs/${club}?member=${member}");
    }
}
