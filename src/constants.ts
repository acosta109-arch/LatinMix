import { NewsItem, GalleryImage } from './types';

export const NEWS_DATA: NewsItem[] = [
  {
    id: '1',
    title: 'El auge de la música latina en el mundo',
    summary: 'Artistas latinos dominan las listas globales de streaming este año, marcando un hito histórico en la industria.',
    image: 'https://images.unsplash.com/photo-1493225255756-d9584f8606e9?auto=format&fit=crop&q=80&w=800',
    date: '24 Mar 2026',
    category: 'Internacional',
    featured: true
  },
  {
    id: '2',
    title: 'Nuevas tendencias en festivales nacionales',
    summary: 'Los festivales locales están adoptando tecnologías inmersivas para mejorar la experiencia del público.',
    image: 'https://images.unsplash.com/photo-1459749411177-042180ce673c?auto=format&fit=crop&q=80&w=800',
    date: '23 Mar 2026',
    category: 'Nacional'
  },
  {
    id: '3',
    title: 'Colaboración histórica entre leyendas del reggaetón',
    summary: 'Dos de los nombres más grandes del género anuncian un álbum conjunto que promete revolucionar el sonido urbano.',
    image: 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&q=80&w=800',
    date: '22 Mar 2026',
    category: 'Internacional'
  },
  {
    id: '4',
    title: 'Impacto de la radio digital en la audiencia joven',
    summary: 'Un estudio revela que el 70% de los jóvenes prefieren sintonizar emisoras a través de plataformas web.',
    image: 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&q=80&w=800',
    date: '21 Mar 2026',
    category: 'Nacional'
  },
  {
    id: '5',
    title: 'Premios Juventud 2026: Nominados confirmados',
    summary: 'Se revelan los artistas que competirán por el galardón más importante de la música joven latina.',
    image: 'https://images.unsplash.com/photo-1514525253361-bee8718a74a2?auto=format&fit=crop&q=80&w=800',
    date: '20 Mar 2026',
    category: 'Internacional'
  }
];

export const GALLERY_DATA: GalleryImage[] = [
  {
    id: 'g1',
    url: 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&q=80&w=800',
    title: 'Concierto en Vivo',
    description: 'La energía del público en el festival Latin Mix.'
  },
  {
    id: 'g2',
    url: 'https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?auto=format&fit=crop&q=80&w=800',
    title: 'Estudio de Grabación',
    description: 'Donde ocurre la magia de Latin Mix Radio.'
  },
  {
    id: 'g3',
    url: 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&q=80&w=800',
    title: 'Entrevista Exclusiva',
    description: 'Conversando con las estrellas del momento.'
  },
  {
    id: 'g4',
    url: 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&q=80&w=800',
    title: 'DJ Set',
    description: 'Mezclando los mejores éxitos latinos.'
  },
  {
    id: 'g5',
    url: 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?auto=format&fit=crop&q=80&w=800',
    title: 'Backstage',
    description: 'Momentos antes de salir al aire.'
  },
  {
    id: 'g6',
    url: 'https://images.unsplash.com/photo-1514525253361-bee8718a74a2?auto=format&fit=crop&q=80&w=800',
    title: 'Evento Especial',
    description: 'Celebrando con nuestra audiencia.'
  }
];
