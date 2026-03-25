import React from 'react';

interface LogoProps {
  className?: string;
  size?: 'sm' | 'md' | 'lg' | 'xl';
}

export const Logo: React.FC<LogoProps> = ({ className = '', size = 'md' }) => {
  const sizeClasses = {
    sm: 'text-lg',
    md: 'text-xl sm:text-2xl',
    lg: 'text-3xl sm:text-4xl',
    xl: 'text-5xl sm:text-7xl'
  };

  return (
    <div className={`flex flex-col leading-none font-black tracking-tighter ${sizeClasses[size]} ${className}`}>
      <div className="flex items-baseline">
        <span className="text-gradient-latin">LATIN</span>
        <span className="text-gradient-mix ml-1">MIX</span>
      </div>
      {size !== 'sm' && (
        <span className="text-[8px] sm:text-[10px] font-bold tracking-[0.3em] text-radio-gray uppercase mt-0.5">
          La emisora de todos
        </span>
      )}
    </div>
  );
};
