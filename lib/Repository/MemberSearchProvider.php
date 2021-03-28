<?php

namespace OCA\SPGVerein\Repository;

use OCA\SPGVerein\AppInfo\Application;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Search\IProvider;
use OCP\Search\ISearchQuery;
use OCP\Search\SearchResult;
use OCP\Search\SearchResultEntry;

class MemberSearchProvider implements IProvider {

    /** @var Club */
    private $club;

    /** @var IL10N */
    private $l10n;

    /** @var IURLGenerator */
    private $urlGenerator;

    function __construct(Club $club, IL10N $l10n, IURLGenerator $urlGenerator) {
        $this->club = $club;
        $this->l10n = $l10n;
        $this->urlGenerator = $urlGenerator;
    }

    public function getId(): string {
        return 'spgvereinsearchprovider';
    }

    public function getName(): string {
        return $this->l10n->t('SPG Verein');
    }

    public function getOrder(string $route, array $routeParameters): int {
        if (strpos($route, Application::APP_ID . '.') === 0) {
            // Active app, prefer my results
            return -1;
        }

        return 55;
    }

    public function search(IUser $user, ISearchQuery $query): SearchResult {
        $term = strtolower($query->getTerm());
        $entries = array();

        $clubs = $this->club->getAllClubs();
        foreach ($clubs as $c) {
            $members = $this->club->getAllMembers($c['id']);
            foreach ($members as $member) {
                if (strpos(strtolower($member->getFullname()), $term) !== false) {
                    array_push($entries, new SearchResultEntry(
                        $this->urlGenerator->linkToRoute('spgverein.page.index'),
                        $member->getFullname(),
                        $c['name'],
                        $this->urlGenerator->linkToRoute(
                            'spgverein.page.redirectToMember',
                            [
                                'club' => $c["id"],
                                'member' => $member->getId(),
                            ]
                        )
                    ));
                }
            }
        }

        return SearchResult::complete($this->l10n->t('My app'), $entries);
    }

}