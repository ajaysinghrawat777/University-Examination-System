import AppLayout from '@/layouts/AppLayout';
import { Link } from '@inertiajs/react';

type Student = {
  id: number;
  admission_no: string;
  name: string;
  programme: string | null;
};

type PaginationLink = {
  url: string | null;
  label: string;
  active: boolean;
};

type PaginatedStudents = {
  data: Student[];
  current_page: number;
  last_page: number;
  from: number | null;
  to: number | null;
  total: number;
  links: PaginationLink[];
};

export default function Index({
  students,
}: {
  students: PaginatedStudents;
}) {
  return (
    <AppLayout>
      <h1 className="mb-6 text-2xl font-bold">Students</h1>

      <div className="overflow-hidden rounded-xl border bg-white">
        <table className="min-w-full text-sm">
          <thead className="bg-slate-100">
            <tr>
              <th className="px-4 py-3 text-left">Admission No.</th>
              <th className="px-4 py-3 text-left">Name</th>
              <th className="px-4 py-3 text-left">Programme</th>
            </tr>
          </thead>

          <tbody>
            {students.data.length > 0 ? (
              students.data.map((s) => (
                <tr key={s.id} className="border-t">
                  <td className="px-4 py-3">{s.admission_no}</td>
                  <td className="px-4 py-3">{s.name}</td>
                  <td className="px-4 py-3">
                    {s.programme ?? '—'}
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td
                  colSpan={3}
                  className="px-4 py-8 text-center text-slate-500"
                >
                  No students found.
                </td>
              </tr>
            )}
          </tbody>
        </table>

        {/* Table footer */}
        <div className="flex flex-col gap-4 border-t px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
          {/* Records information */}
          <div className="text-sm text-slate-600">
            {students.total > 0 ? (
              <>
                Showing{' '}
                <span className="font-medium text-slate-900">
                  {students.from}
                </span>{' '}
                to{' '}
                <span className="font-medium text-slate-900">
                  {students.to}
                </span>{' '}
                of{' '}
                <span className="font-medium text-slate-900">
                  {students.total}
                </span>{' '}
                students
              </>
            ) : (
              'Showing 0 students'
            )}
          </div>

          {/* Pagination */}
          {students.last_page > 1 && (
            <div className="flex flex-wrap items-center gap-1">
              {students.links.map((link, index) => {
                const isPrevious = index === 0;
                const isNext =
                  index === students.links.length - 1;

                const label = isPrevious
                  ? '« Previous'
                  : isNext
                    ? 'Next »'
                    : link.label;

                if (!link.url) {
                  return (
                    <span
                      key={`${link.label}-${index}`}
                      className="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-400"
                    >
                      {label}
                    </span>
                  );
                }

                return (
                  <Link
                    key={`${link.label}-${index}`}
                    href={link.url}
                    preserveScroll
                    className={`rounded-lg border px-3 py-1.5 text-sm transition ${
                      link.active
                        ? 'border-slate-900 bg-slate-900 text-white'
                        : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50'
                    }`}
                  >
                    {label}
                  </Link>
                );
              })}
            </div>
          )}
        </div>
      </div>
    </AppLayout>
  );
}