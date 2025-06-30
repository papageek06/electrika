<?php

namespace App\Service;

use App\Repository\AbsenceRepository;
use App\Repository\EventDetailRepository;
use App\Repository\EventRepository;
use App\Repository\InterventionTeamRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PlanningService
{
    private EventRepository $eventRepository;
    private EventDetailRepository $eventDetailRepository;
    private InterventionTeamRepository $interventionTeamRepository;
    private AbsenceRepository $absenceRepository;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        EventRepository $eventRepository,
        EventDetailRepository $eventDetailRepository,
        InterventionTeamRepository $interventionTeamRepository,
        AbsenceRepository $absenceRepository,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->eventRepository = $eventRepository;
        $this->eventDetailRepository = $eventDetailRepository;
        $this->interventionTeamRepository = $interventionTeamRepository;
        $this->absenceRepository = $absenceRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function generateCalendarData(): array
    {
        return [
            'events' => $this->getEventData(),
            'eventDetails' => $this->getEventDetailData(),
            'absences' => $this->getAbsenceData(),
            'interventionTeams' => $this->getInterventionTeamData(),
        ];
    }

    private function getEventData(): array
    {
        $eventData = [];
        foreach ($this->eventRepository->findAll() as $event) {
            // Montage
            $eventData[] = [
                'title' => $event->getName() . ' - Montage',
                'start' => $event->getDateMontage()->format('Y-m-d'),
                'end' => $event->getDateStartShow()->format('Y-m-d'),
                'backgroundColor' => '#A0522D', // brown
                'extendedProps' => [
                    'type' => 'event',
                    'eventId' => $event->getId()
                ],
                'url' => $this->urlGenerator->generate('app_event_show', ['id' => $event->getId()])
            ];

            // Show
            $eventData[] = [
                'title' => $event->getName() . ' - Show',
                'start' => $event->getDateStartShow()->format('Y-m-d'),
                'end' => $event->getDateEndSHOW()->format('Y-m-d'),
                'backgroundColor' => '#FFD700', // gold
                'extendedProps' => [
                    'type' => 'event',
                    'eventId' => $event->getId()
                ],
                'url' => $this->urlGenerator->generate('app_event_show', ['id' => $event->getId()])
            ];

            // Démontage
            $eventData[] = [
                'title' => $event->getName() . ' - Démontage',
                'start' => $event->getDateEndSHOW()->format('Y-m-d'),
                'end' => $event->getDateEnd()->modify('+1 day')->format('Y-m-d'),
                'backgroundColor' => '#FF8C00', // dark orange
                'extendedProps' => [
                    'type' => 'event',
                    'eventId' => $event->getId()
                ],
                'url' => $this->urlGenerator->generate('app_event_show', ['id' => $event->getId()])
            ];
        }
        return $eventData;
    }

    private function getEventDetailData(): array
    {
        $eventDetailData = [];
        $colorMap = [
            'bl' => '#228B22', // green
            'bp' => '#FFA500', // orange
            'new' => '#1E90FF', // blue
            'br' => '#DC143C'  // red
        ];

        foreach ($this->eventDetailRepository->findByEventDetailDistinct() as $item) {
            $eventDetailData[] = [
                'title' => $item['name'],
                'start' => $item['date']->format('Y-m-d'),
                'end' => $item['date']->modify('+1 day')->format('Y-m-d'),
                'backgroundColor' => $colorMap[$item['mouve']] ?? '#808080', // grey as fallback
                'extendedProps' => [
                    'type' => 'eventDetail',
                    'eventId' => $item['eventId'],
                    'mouvement' => $item['mouve']
                ],
                'url' => $this->urlGenerator->generate('app_event_show', ['id' => $item['eventId']])
            ];
        }
        return $eventDetailData;
    }

    private function getAbsenceData(): array
    {
        $absenceData = [];
        foreach ($this->absenceRepository->findAll() as $absence) {
            $absenceData[] = [
                'title' => $absence->getTechnicians()->getUser()->getfirstName() . ' - ' . $absence->getType(),
                'start' => $absence->getStartDate()->format('Y-m-d'),
                'end' => $absence->getEndDate()->modify('+1 day')->format('Y-m-d'),
                'backgroundColor' => '#DC3545',
                'textColor' => '#fff',
                'extendedProps' => [
                    'type' => 'absence',
                    'comment' => $absence->getComment()
                ],
                'url' => $this->urlGenerator->generate('app_absence_show', ['id' => $absence->getId()])
            ];
        }
        return $absenceData;
    }

    private function getInterventionTeamData(): array
    {
        $teamData = [];
        foreach ($this->interventionTeamRepository->findAll() as $team) {
            $teamData[] = [
                'title' => 'Equipe - ' . $team->getEvent()->getName(),
                'start' => $team->getStartDate()->format('Y-m-d'),
                'end' => $team->getEndDate()->modify('+1 day')->format('Y-m-d'),
                'backgroundColor' => '#20C997',
                'extendedProps' => [
                    'type' => 'interventionTeam',
                    'eventId' => $team->getEvent()->getId()
                ],
                'url' => $this->urlGenerator->generate('app_intervention_team_show', ['id' => $team->getId()])
            ];
        }
        return $teamData;
    }
}
