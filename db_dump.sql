--
-- PostgreSQL database dump
--

-- Dumped from database version 12.2 (Debian 12.2-2.pgdg100+1)
-- Dumped by pg_dump version 12.2 (Ubuntu 12.2-2.pgdg18.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: mark; Type: TABLE; Schema: public; Owner: picplace
--

CREATE TABLE public.mark (
    userid character varying(129) NOT NULL,
    phid bigint NOT NULL,
    value smallint NOT NULL
);


ALTER TABLE public.mark OWNER TO picplace;

--
-- Name: photo; Type: TABLE; Schema: public; Owner: picplace
--

CREATE TABLE public.photo (
    phid bigint NOT NULL,
    path character varying(255) NOT NULL,
    ownerlogin character varying(129) NOT NULL,
    description character varying(500),
    addtime timestamp without time zone NOT NULL
);


ALTER TABLE public.photo OWNER TO picplace;

--
-- Name: photo_phid_seq; Type: SEQUENCE; Schema: public; Owner: picplace
--

CREATE SEQUENCE public.photo_phid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.photo_phid_seq OWNER TO picplace;

--
-- Name: photo_phid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: picplace
--

ALTER SEQUENCE public.photo_phid_seq OWNED BY public.photo.phid;


--
-- Name: subs; Type: TABLE; Schema: public; Owner: picplace
--

CREATE TABLE public.subs (
    login character varying(129) NOT NULL,
    sublogin character varying(129) NOT NULL
);


ALTER TABLE public.subs OWNER TO picplace;

--
-- Name: usr; Type: TABLE; Schema: public; Owner: picplace
--

CREATE TABLE public.usr (
    login character varying(129) NOT NULL,
    hpasswd character varying(60) NOT NULL,
    name character varying(30) NOT NULL,
    surname character varying(30) NOT NULL,
    bio character varying(500),
    profilepicpath character varying(255),
    nick character varying(30) NOT NULL
);


ALTER TABLE public.usr OWNER TO picplace;

--
-- Name: photo phid; Type: DEFAULT; Schema: public; Owner: picplace
--

ALTER TABLE ONLY public.photo ALTER COLUMN phid SET DEFAULT nextval('public.photo_phid_seq'::regclass);


--
-- Data for Name: mark; Type: TABLE DATA; Schema: public; Owner: picplace
--

COPY public.mark (userid, phid, value) FROM stdin;
\.


--
-- Data for Name: photo; Type: TABLE DATA; Schema: public; Owner: picplace
--

COPY public.photo (phid, path, ownerlogin, description, addtime) FROM stdin;
\.


--
-- Data for Name: subs; Type: TABLE DATA; Schema: public; Owner: picplace
--

COPY public.subs (login, sublogin) FROM stdin;
\.


--
-- Data for Name: usr; Type: TABLE DATA; Schema: public; Owner: picplace
--

COPY public.usr (login, hpasswd, name, surname, bio, profilepicpath, nick) FROM stdin;
\.


--
-- Name: photo_phid_seq; Type: SEQUENCE SET; Schema: public; Owner: picplace
--

SELECT pg_catalog.setval('public.photo_phid_seq', 81, true);


--
-- Name: mark mark_pkey; Type: CONSTRAINT; Schema: public; Owner: picplace
--

ALTER TABLE ONLY public.mark
    ADD CONSTRAINT mark_pkey PRIMARY KEY (userid, phid);


--
-- Name: photo photo_pkey; Type: CONSTRAINT; Schema: public; Owner: picplace
--

ALTER TABLE ONLY public.photo
    ADD CONSTRAINT photo_pkey PRIMARY KEY (phid);


--
-- Name: subs subs_pkey; Type: CONSTRAINT; Schema: public; Owner: picplace
--

ALTER TABLE ONLY public.subs
    ADD CONSTRAINT subs_pkey PRIMARY KEY (login, sublogin);


--
-- Name: usr usr_pkey; Type: CONSTRAINT; Schema: public; Owner: picplace
--

ALTER TABLE ONLY public.usr
    ADD CONSTRAINT usr_pkey PRIMARY KEY (login);


--
-- Name: mark mark_phid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: picplace
--

ALTER TABLE ONLY public.mark
    ADD CONSTRAINT mark_phid_fkey FOREIGN KEY (phid) REFERENCES public.photo(phid);


--
-- Name: mark mark_userid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: picplace
--

ALTER TABLE ONLY public.mark
    ADD CONSTRAINT mark_userid_fkey FOREIGN KEY (userid) REFERENCES public.usr(login);


--
-- Name: photo photo_ownerlogin_fkey; Type: FK CONSTRAINT; Schema: public; Owner: picplace
--

ALTER TABLE ONLY public.photo
    ADD CONSTRAINT photo_ownerlogin_fkey FOREIGN KEY (ownerlogin) REFERENCES public.usr(login);


--
-- Name: subs subs_login_fkey; Type: FK CONSTRAINT; Schema: public; Owner: picplace
--

ALTER TABLE ONLY public.subs
    ADD CONSTRAINT subs_login_fkey FOREIGN KEY (login) REFERENCES public.usr(login);


--
-- Name: subs subs_sublogin_fkey; Type: FK CONSTRAINT; Schema: public; Owner: picplace
--

ALTER TABLE ONLY public.subs
    ADD CONSTRAINT subs_sublogin_fkey FOREIGN KEY (sublogin) REFERENCES public.usr(login);


--
-- PostgreSQL database dump complete
--

