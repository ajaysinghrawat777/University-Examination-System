import AppLayout from '@/layouts/AppLayout';
import { Link } from '@inertiajs/react';

type Exam = {
  id: number;
  code: string;
  name: string;
  academic_year: string;
  term: string;
  status: string;
  enrolled_count: number;
};

type PaginationLink = {
  url: string | null;
  label: string;
  active: boolean;
};

type PaginatedExaminations = {
  data: Exam[];
  current_page: number;
  from: number | null;
  last_page: number;
  per_page: number;
  to: number | null;
  total: number;
  links: PaginationLink[];
};

export default function Index({
  examinations,
}: {
  examinations: PaginatedExaminations;
}) {
  return (
    <AppLayout>
      <div className="mb-6 flex items-center justify-between">
        <h1 className="text-2xl font-bold">Examinations</h1>
      </div>

      <div className="overflow-hidden rounded-xl border bg-white">
        <table className="min-w-full text-sm">
          <thead className="bg-slate-100">
            <tr>
              {[
                'Code',
                'Name',
                'Year',
                'Term',
                'Status',
                'Students',
                '',
              ].map((h) => (
                <th
                  key={h}
                  className="px-4 py-3 text-left font-medium"
                >
                  {h}
                </th>
              ))}
            </tr>
          </thead>

          <tbody>
            {examinations.data.length > 0 ? (
              examinations.data.map((ex) => (
                <tr key={ex.id} className="border-t">
                  <td className="px-4 py-3 font-medium">
                    {ex.code}
                  </td>

                  <td className="px-4 py-3">
                    {ex.name}
                  </td>

                  <td className="px-4 py-3">
                    {ex.academic_year}
                  </td>

                  <td className="px-4 py-3">
                    {ex.term}
                  </td>

                  <td className="px-4 py-3">
                    {ex.status}
                  </td>

                  <td className="px-4 py-3">
                    {ex.enrolled_count}
                  </td>

                  <td className="px-4 py-3">
                    <Link
                      className="underline"
                      href={`/examinations/${ex.id}`}
                    >
                      Open
                    </Link>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td
                  colSpan={7}
                  className="px-4 py-8 text-center text-slate-500"
                >
                  No examinations found.
                </td>
              </tr>
            )}
          </tbody>
        </table>

        {/* Bottom information + pagination */}
        <div className="flex flex-col gap-4 border-t px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
          {/* Records information */}
          <div className="text-sm text-slate-600">
            {examinations.total > 0 ? (
              <>
                Showing{' '}
                <span className="font-medium text-slate-900">
                  {examinations.from}
                </span>{' '}
                to{' '}
                <span className="font-medium text-slate-900">
                  {examinations.to}
                </span>{' '}
                of{' '}
                <span className="font-medium text-slate-900">
                  {examinations.total}
                </span>{' '}
                examinations
              </>
            ) : (
              'Showing 0 examinations'
            )}
          </div>

          {/* Pagination */}
          {examinations.last_page > 1 && (
            <div className="flex flex-wrap items-center gap-1">
              {examinations.links.map((link, index) => {
                const isPrevious = index === 0;
                const isNext =
                  index === examinations.links.length - 1;

                return link.url ? (
                  <Link
                    key={index}
                    href={link.url}
                    preserveScroll
                    className={`rounded-lg border px-3 py-1.5 text-sm ${
                      link.active
                        ? 'border-slate-900 bg-slate-900 text-white'
                        : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50'
                    }`}
                    dangerouslySetInnerHTML={{
                      __html: isPrevious
                        ? '&laquo; Previous'
                        : isNext
                          ? 'Next &raquo;'
                          : link.label,
                    }}
                  />
                ) : (
                  <span
                    key={index}
                    className="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-400"
                    dangerouslySetInnerHTML={{
                      __html: isPrevious
                        ? '&laquo; Previous'
                        : isNext
                          ? 'Next &raquo;'
                          : link.label,
                    }}
                  />
                );
              })}
            </div>
          )}
        </div>
      </div>
    </AppLayout>
  );
}
