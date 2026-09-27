import { Link } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';

export default function AppLayout({ children }: PropsWithChildren) {
  return (
    <div className="min-h-screen bg-slate-50 text-slate-900">
      <header className="border-b bg-white">
        <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
          <Link href="/dashboard" className="text-lg font-semibold">University Examination System</Link>
          <nav className="flex gap-4 text-sm">
            <Link href="/examinations" className="hover:underline">Examinations</Link>
            <Link href="/students" className="hover:underline">Students</Link>
          </nav>
        </div>
      </header>
      <main className="mx-auto max-w-7xl px-6 py-8">{children}</main>
    </div>
  );
}
