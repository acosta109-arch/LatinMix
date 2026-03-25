export interface NewsItem {
  id: string;
  title: string;
  summary: string;
  image: string;
  date: string;
  category: 'Nacional' | 'Internacional';
  featured?: boolean;
}

export interface GalleryImage {
  id: string;
  url: string;
  title: string;
  description: string;
}
