import tw from 'twin.macro';
import { useNavigate, useParams } from 'react-router-dom';
import { Form, Formik, useFormikContext } from 'formik';
import useSWR from 'swr';
import { TrashIcon } from '@heroicons/react/outline';
import { Shape } from '@/components/elements/button/types';

import { Button } from '@/components/elements/button/index';
import deletePort from '@/api/admin/ports/deletePort';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import getPort from '@/api/admin/ports/getPort';
import AdminContentBlock from '@/components/admin/AdminContentBlock';
import FlashMessageRender from '@/components/FlashMessageRender';
import useFlash from '@/plugins/useFlash';
import PortBaseSettings from './PortBaseSettings';
import AdvPortSettings from './AdvPortSettings';

function InternalForm({ onDelete }: { onDelete: () => void }) {
    const { isSubmitting, isValid } = useFormikContext<any>();

    return (
        <Form>
            <div css={tw`grid grid-cols-2 gap-y-6 gap-x-8 mb-16`}>
                <div css={tw`grid grid-cols-1 gap-y-6 col-span-2 md:col-span-1`}>
                    <PortBaseSettings />
                </div>
                <div css={tw`grid grid-cols-1 gap-y-6 col-span-2 md:col-span-1`}>
                    <AdvPortSettings />
                </div>

                <div css={tw`bg-neutral-700 rounded shadow-md px-4 py-3 col-span-2`}>
                    <div css={tw`flex flex-row justify-between`}>
                        <Button.Danger type="button" shape={Shape.IconSquare} onClick={onDelete}>
                            <TrashIcon css={tw`w-1/2 h-1/2`} />
                        </Button.Danger>
                        <Button type="submit" css={tw`ml-auto`} disabled={isSubmitting || !isValid || true}>
                            Update Port
                        </Button>
                    </div>
                </div>
            </div>
        </Form>
    );
}

export default function PortEditContainer() {
    const navigate = useNavigate();
    const params = useParams<'id'>();
    const {
        data: port,
        error,
        isLoading,
    } = useSWR([params.id, 'ports-edit'], (args: [string | undefined, ...any]) => {
        if (args[0]) return getPort(parseInt(args[0]));
        throw new Error('Not Found');
    });

    const { clearFlashes, clearAndAddHttpError } = useFlash();

    const submit = () => {
        clearFlashes('port:edit');
    };

    const onDelete = () => {
        clearFlashes('port:edit');
        if (!params?.id) return;
        deletePort(parseInt(params.id))
            .then(() => navigate('/admin/ports'))
            .catch(error => clearAndAddHttpError({ key: 'port:edit', error }));
    };

    return (
        <AdminContentBlock title="New Port">
            <div css={tw`w-full flex flex-row items-center mb-8`}>
                <div css={tw`flex flex-col flex-shrink`} style={{ minWidth: '0' }}>
                    <h2 css={tw`text-2xl text-neutral-50 font-header font-medium`}>New Port</h2>
                    <p css={tw`text-base text-neutral-400 whitespace-nowrap overflow-ellipsis overflow-hidden`}>
                        Add a new port to the panel.
                    </p>
                </div>
            </div>

            <FlashMessageRender byKey={'port:edit'} css={tw`mb-4`} />

            {error || isLoading ? (
                <SpinnerOverlay visible={true} />
            ) : (
                <Formik onSubmit={submit} initialValues={port as any}>
                    <InternalForm onDelete={onDelete} />
                </Formik>
            )}
        </AdminContentBlock>
    );
}
