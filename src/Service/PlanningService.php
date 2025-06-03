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
        $calendarData = [];

        // Events
        foreach ($this->eventRepository->findAll() as $event) {
            $calendarData[] = [
                'title' => $event->getName() . ' - Montage',
                'start' => $event->getDateMontage()->format('Y-m-d'),
                'end' => $event->getDateStartShow()->format('Y-m-d'),
                'backgroundColor' => '#A0522D',
                'extendedProps' => ['type' => 'event'],
                'url' => $this->urlGenerator->generate('app_event_show', ['id' => $event->getId()])
            ];
            $calendarData[] = [
                'title' => $event->getName() . ' - Show',
                'start' => $event->getDateStartShow()->format('Y-m-d'),
                'end' => $event->getDateEndSHOW()->modify('+1 day')->format('Y-m-d'),
                'backgroundColor' => '#FFD700',
                'extendedProps' => ['type' => 'event'],
                'url' => $this->urlGenerator->generate('app_event_show', ['id' => $event->getId()])
            ];
            $calendarData[] = [
                'title' => $event->getName() . ' - Démontage',
                'start' => $event->getDateEndSHOW()->format('Y-m-d'),
                'end' => $event->getDateEnd()->modify('+1 day')->format('Y-m-d'),
                'backgroundColor' => '#FF8C00',
                'extendedProps' => ['type' => 'event'],
                'url' => $this->urlGenerator->generate('app_event_show', ['id' => $event->getId()])
            ];
        }

        // EventDetails (grouped)
        foreach ($this->eventDetailRepository->findByEventDetailDistinct() as $item) {
            $colorMap = ['bl' => 'green', 'bp' => 'orange', 'new' => 'blue', 'br' => 'red'];
            $calendarData[] = [
                'title' => $item['name'],
                'start' => $item['date']->format('Y-m-d'),
                'end' => $item['date']->modify('+1 day')->format('Y-m-d'),
                'backgroundColor' => $colorMap[$item['mouve']] ?? 'grey',
                // 'url' => $this->urlGenerator->generate('app_event_detail_show', ['id' => $item->getEvent()]),
                'extendedProps' => [
                    'type' => 'eventDetail',
                    'mouvement' => $item['mouve']
                ]
            ];
        }

        // Absences
        foreach ($this->absenceRepository->findAll() as $absence) {
            $calendarData[] = [
                'title' => $absence->getTechnician()->getUser()->getPrenom() . ' - ' . $absence->getType(),
                'start' => $absence->getStartDate()->format('Y-m-d'),
                'end' => $absence->getEndDate()->modify('+1 day')->format('Y-m-d'),
                'backgroundColor' => '#DC3545',
                'textColor' => '#fff',
                'extendedProps' => [
                    'type' => 'absence',
                    'comment' => $absence->getComment()
                ],
                'url' => $this->urlGenerator->generate('app_event_show', ['id' => $absence->getId()])
            ];
        }

        // Intervention Teams
        foreach ($this->interventionTeamRepository->findAll() as $team) {
            $calendarData[] = [
                'title' => 'Equipe - ' . $team->getEvent()->getName(),
                'start' => $team->getStartDate()->format('Y-m-d'),
                'end' => $team->getEndDate()->modify('+1 day')->format('Y-m-d'),
                'backgroundColor' => '#20C997',
                'extendedProps' => [
                    'type' => 'interventionTeam'
                ],
                'url' => $this->urlGenerator->generate('app_event_show', ['id' => $team->getId()])
            ];
        }

        return $calendarData;
    }
}
