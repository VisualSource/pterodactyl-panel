import { type Actions, type Action, action, createContextStore, useStoreActions } from 'easy-peasy';
import type { FormikHelpers } from 'formik';
import { Form, Formik } from 'formik';
import { useEffect, useState } from 'react';
import tw from 'twin.macro';
import { object, number } from 'yup';

import { useNavigate, useParams } from 'react-router-dom';

import getDomain from '@/api/admin/domains/getDomain';
import { Button } from '@/components/elements/button/index';
import FlashMessageRender from '@/components/FlashMessageRender';
import ServerSelect from '@/components/admin/domains/ServerSelect';
import { type Domain, type ServerLike } from '@/api/admin/domains/getDomains';
import updateDomain from '@/api/admin/domains/updateDomain';
import Field from '@/components/elements/Field';
import { ApplicationStore } from '@/state';
import AdminContentBlock from '../AdminContentBlock';
import Spinner from '@/components/elements/Spinner';
import AdminBox from '../AdminBox';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import { type Server } from '@/api/admin/servers/getServers';
import DomainDeleteButton from './DomainDeleteButton';

interface Values {
    server: Server | ServerLike | null;
    domain: string;
}

interface Ctx {
    domain: Domain | undefined;
    setDomain: Action<Ctx, Domain | undefined>;
}

const schema = object().shape({
    server_id: number()
        .notRequired()
        .nullable()
        .integer('Server Id must be a integer.')
        .positive('Must be a positive number.'),
});

export const Context = createContextStore<Ctx>({
    domain: undefined,
    setDomain: action((state, payload) => {
        state.domain = payload;
    }),
});

function EditContainer() {
    const navigate = useNavigate();

    const { clearFlashes, clearAndAddHttpError, addFlash } = useStoreActions(
        (actions: Actions<ApplicationStore>) => actions.flashes,
    );

    const domain = Context.useStoreState(state => state.domain);
    const setDomain = Context.useStoreActions(actions => actions.setDomain);

    if (!domain) return null;

    const submit = ({ server }: Values, { setSubmitting }: FormikHelpers<Values>) => {
        clearFlashes('domain');

        updateDomain(domain.id, server?.id ?? null)
            .then(() =>
                setDomain({
                    ...domain,
                    server_id: server?.id ?? null,
                    server: server ? { id: server.id, name: server.name, identifier: server.identifier } : null,
                }),
            )
            .catch(error => {
                console.error(error);
                clearAndAddHttpError({ key: 'domain', error });
            })
            .then(() => setSubmitting(false));
    };

    return (
        <Formik
            onSubmit={submit}
            initialValues={{
                domain: domain.domain,
                server: domain.server,
            }}
            validationSchema={schema}
        >
            {({ isSubmitting, isValid, initialValues }) => (
                <>
                    <AdminBox title="Edit Domain" css={tw`relative`}>
                        <SpinnerOverlay visible={isSubmitting} />

                        <Form css={tw`mb-0`}>
                            <div>
                                <Field id="domain" name="domain" label="Domain" type="text" disabled readOnly />
                            </div>
                            <div css={tw`mt-6`}>
                                <ServerSelect selected={initialValues.server} />
                            </div>

                            <div css={tw`w-full flex flex-row items-center mt-6`}>
                                <div css={tw`flex`}>
                                    <DomainDeleteButton
                                        domainId={domain.id}
                                        onDeleted={() => {
                                            addFlash({
                                                key: 'domains',
                                                message: `Subdomain (${domain.domain}) will be completly removed in 2-5 minutes.`,
                                                type: 'success',
                                            });
                                            navigate('/admin/domains');
                                        }}
                                    />
                                </div>

                                <div css={tw`flex ml-auto`}>
                                    <Button type="submit" disabled={isSubmitting || !isValid}>
                                        Save Changes
                                    </Button>
                                </div>
                            </div>
                        </Form>
                    </AdminBox>
                </>
            )}
        </Formik>
    );
}

function DomainEditContainer() {
    const params = useParams<'id'>();
    const [loading, setLoading] = useState<boolean>(true);

    const { clearFlashes, clearAndAddHttpError } = useStoreActions(
        (actions: Actions<ApplicationStore>) => actions.flashes,
    );

    const domain = Context.useStoreState(state => state.domain);
    const setDomain = Context.useStoreActions(actions => actions.setDomain);

    useEffect(() => {
        clearFlashes('domain');

        getDomain(Number(params.id), ['server'])
            .then(domain => setDomain(domain))
            .catch(error => {
                console.error(error);
                clearAndAddHttpError({ key: 'domain', error });
            })
            .then(() => setLoading(false));
    }, []);

    if (loading || !domain) {
        return (
            <AdminContentBlock>
                <FlashMessageRender byKey={'domain'} css={tw`mb-4`} />

                <div css={tw`w-full flex flex-col items-center justify-center`} style={{ height: '24rem' }}>
                    <Spinner size={'base'} />
                </div>
            </AdminContentBlock>
        );
    }

    return (
        <AdminContentBlock title={`Domain - ${domain.domain}`}>
            <div css={tw`w-full flex flex-row items-center mb-8`}>
                <div css={tw`flex flex-col flex-shrink`} style={{ minWidth: '0' }}>
                    <h2 css={tw`text-2xl text-neutral-50 font-header font-medium`}>{domain.domain}.titanhosting.us</h2>
                    <p css={tw`text-base text-neutral-400`}>
                        <span css={tw`italic`}>{domain.domain}</span>
                    </p>
                </div>
            </div>

            <EditContainer />

            <FlashMessageRender byKey={'domain'} css={tw`mb-4`} />
        </AdminContentBlock>
    );
}

export default () => (
    <Context.Provider>
        <DomainEditContainer />
    </Context.Provider>
);
